<?php

namespace App\Controller;

use App\Form\ApiForm;
use App\Form\ApiDefineUrl;
use App\Form\ApiVarsValuesForm;
use App\Form\ApiDeleteUrlForm;
use App\Form\ApiDeleteVarForm;
use App\Form\ApiSendDefinedForm;
use App\Entity\ApiUrls;
use App\Entity\ApiVarsValues;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @Route("/apirequest", name="apirequest.")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="/")
     */
    public function index()
    {
        return $this->redirect($this->generateUrl('apirequest.define'));
    }

    /////////////////////////////////////// Following 3 methods are used in 'define' method
    //returns array urls for specific user
    public function read_urls($user)
    {
        $repository = $this->getDoctrine()->getRepository(ApiUrls::class);
        $urls = $repository->findByUserkey($user);
        return $urls;
    }
    //returns array of ApiVarsValues for specific url_id
    public function read_vars($url_id)
    {
        $repository = $this->getDoctrine()->getRepository(ApiVarsValues::class);
        $vars = $repository->findBy(['urlkey' => $url_id]);
        return $vars;
    }
    //returns array with urls and per each url (index 0) there is array with keys/values (index 1)
    public function join_urls_vars($user)
    {
        $joined = array();
        foreach ($this->read_urls($user) as $value )
        {
            $url_id = $value->getId();
            array_push($joined, [ $value,  $this->read_vars($url_id)]);
        }
        return $joined;
    }

    /**
     * @Route("/define", name="define")
     * @param Request $request
     */
    public function define(Request $request, UserInterface $user)
    {
        $url_post = new ApiUrls();
        $url_post->setUserkey($user);
        $url_form = $this->createForm(ApiDefineUrl::class, $url_post);

        $var_post = new ApiVarsValues();
        $var_form = $this->createForm(ApiVarsValuesForm::class, 
                                     $var_post, 
                                     array('user' => $this->getUser())); //i pass userkey to filter form choicetype by user

        $delete_url_form = $this->createForm(ApiDeleteUrlForm::class);
        $delete_var_form = $this->createForm(ApiDeleteVarForm::class);

        $entity = $this->getDoctrine()->getManager();

        $url_form->handleRequest($request);                     
        if ($url_form->isSubmitted()  && $url_form->isValid()){     //URL form
            $entity->persist($url_post);
            $entity->flush();

            $this->addFlash('success', $request->request->get('api_define_url')["name"].' has been saved.');
            return $this->redirect($this->generateUrl('apirequest.define'));
            }

        $var_form->handleRequest($request);
        if ($var_form->isSubmitted()  && $var_form->isValid()){     //Variables-Values form
            $entity->persist($var_post);
            $entity->flush();

            $this->addFlash('success', $request->request->get('api_vars_values_form')["var"].' has been saved.');
            return $this->redirect($this->generateUrl('apirequest.define'));
            }

        $delete_url_form->handleRequest($request);
        if ($delete_url_form->isSubmitted()  && $delete_url_form->isValid()){     //Delete URL form
            $repository = $this->getDoctrine()->getRepository(ApiUrls::class);
            $url_id = $request->request->get('api_delete_url_form')["url_id"];
            $url = $repository->findOneBy(['id' => $url_id, 'userkey' => $user]);
            $entity->remove($url);
            $entity->flush();

            $this->addFlash('success', 'URL: '.$url->getName().' has been deleted.');
            return $this->redirect($this->generateUrl('apirequest.define'));
            }

        $delete_var_form->handleRequest($request);
        if ($delete_var_form->isSubmitted()  && $delete_var_form->isValid()){     //Delete Variables form
            $repository = $this->getDoctrine()->getRepository(ApiVarsValues::class);
            $var_id = $request->request->get('api_delete_var_form')["var_id"];
            $var = $repository->findOneBy(['id' => $var_id]);
            $entity->remove($var);
            $entity->flush();

            $this->addFlash('success', 'Key/Variable pair: '.$var->getName().' has been deleted.');
            return $this->redirect($this->generateUrl('apirequest.define'));
            }

   
        return $this->render('api/define.html.twig', [
            'url_form' => $url_form->createView(),
            'var_form' => $var_form->createView(),
            'delete_url_formObj' => $delete_url_form,
            'delete_var_formObj' => $delete_var_form,
            'urls' => $this->join_urls_vars($user),
        ]);   
    }

        /**
     * @Route("/send_defined", name="send_defined")
     * @param Request $request
     */
    public function send_defined(Request $request)
    {
        $vars_values = new ApiVarsValues();
        //I map the form to $vars_values, so dynamic choice based on url works
        $form = $this->createForm(ApiSendDefinedForm::class, $vars_values, array('user' => $this->getUser()) );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){ 

            $url_id = $request->request->get('api_send_defined_form')["urlkey"];
            $var_value_id = $request->request->get('api_send_defined_form')["var"];
            $type = $request->request->get('api_send_defined_form')["type"];  // GET or POST

            $repository = $this->getDoctrine()->getRepository(ApiUrls::class);
            $url = $repository->find($url_id)->getUrl();
            $repository = $this->getDoctrine()->getRepository(ApiVarsValues::class);
            $var = $repository->find($var_value_id)->getVar();
            $value = $repository->find($var_value_id)->getValue();

            $var_tab = explode(';',$var);    // if vars/values are  ';' separated
            $value_tab = explode(';',$value);
            try {
                $var_value_tab = array_combine($var_tab, $value_tab);
            }
            catch (\ErrorException $e){
                $this->addFlash('success', 'Wrong number of keys/values');
                return $this->redirect($this->generateUrl('apirequest.sendany'));
            }

            $response = $this->CallAPI($type, $url, $data = $var_value_tab);
            $json_resp = json_encode(json_decode($response, true));        //looks prettier this way
            $answer = $json_resp != 'null' ? $json_resp : $response;      //if json cannot decode, $json_resp is null, so i assign string response

            $this->addFlash('success', $type.' sent to URL: '.$url." gave response:\n".$answer);
            return $this->redirect($this->generateUrl('apirequest.send_defined'));
            }
        
        return $this->render('api/send_defined.html.twig', [
            'form' => $form->createView(),
        ]);   
    }

    /**
     * @Route("/sendany", name="sendany")
     * @param Request $request
     */
    public function sendany(Request $request)
    {
        $form = $this->createForm(ApiForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()){ 
            $url = $request->request->get('api_form')["url"];
            $var = $request->request->get('api_form')["var"];
            $value = $request->request->get('api_form')["value"];
            $type = $request->request->get('api_form')["type"];  // GET or POST

            $var_tab = explode(';',$var);       // if vars/values are  ';' separated
            $value_tab = explode(';',$value);
            try {
                $var_value_tab = array_combine($var_tab, $value_tab);
            }
            catch (\ErrorException $e){
                $this->addFlash('success', 'Wrong number of keys/values');
                return $this->redirect($this->generateUrl('apirequest.sendany'));
            }
            $response = $this->CallAPI($type, $url, $data = $var_value_tab);
            $json_resp = json_encode(json_decode($response, true));        //looks prettier this way
            $answer = $json_resp != 'null' ? $json_resp : $response;      //if json cannot decode, $json_resp is null, so i assign string response

            $this->addFlash('success', $type.' sent to URL: '.$url." gave response:\n".$answer);
            return $this->redirect($this->generateUrl('apirequest.sendany'));
    
            }
        
        return $this->render('api/sendany.html.twig', [
            'form' => $form->createView(),
        ]);   
    }

    public function CallAPI($method, $url, $data = false) 
    { 
        $curl = curl_init(); 
        switch ($method) 
        { 
            case "POST": 
                curl_setopt($curl, CURLOPT_POST, 1); 
                if ($data) 
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
                break; 
            case "PUT": 
                curl_setopt($curl, CURLOPT_PUT, 1); 
                break; 
            default: 
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        } 
        // Optional Authentication: 
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
        //curl_setopt($curl, CURLOPT_USERPWD, "username:password"); 

        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($curl);

        if(curl_errno($curl))
            return 'Curl error: '.curl_error($curl); 
        if(curl_getinfo($curl)["http_code"] != 200)
            return 'I received HTTP CODE: '.curl_getinfo($curl)["http_code"];
    
        curl_close($curl); 

        return $result; 
    } 
}
