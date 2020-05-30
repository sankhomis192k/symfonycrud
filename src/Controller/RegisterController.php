<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Form;

class RegisterController extends AbstractController
{
    
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $req,UserPasswordEncoderInterface $passencoder)
    {
        
        $form = $this->createFormBuilder()        	
        	->add("email",EmailType::class,[        		
        		"required"=>true,
        		"attr"=>[ "class"=>"from-control"]
        	])
        	->add("password", RepeatedType::class,[
        		"type"=> PasswordType::class,
        		"required"=> true,
        		"invalid_message" => "The password fields must match.",
        		"options" => ["attr" => ["class" => "password-field from-control"]],
        		"first_options"=> ["label"=>"Password"],
        		"second_options"=> ["label"=>"Confirm Password"]
        	])
        	->add("sign_up",SubmitType::class,[
        		"attr"=>[
        			"class"=> "btn btn-success btn-block"
        		]
        	])
        	->getForm();

        $form->handleRequest($req);


        if($form->isSubmitted()){
        	if($form->isValid()){
        		$data = $form->getData();
	        	$user = new User();
	        	
	        	$user->setEmail($data['email']);
	        	$user->setPassword(
	        		$passencoder->encodePassword($user,$data['password'])
	        	);
	        	$entityManager = $this->getDoctrine()->getManager();
	        	$entityManager->persist($user);
	        	$entityManager->flush();
	        	
	        	return $this->redirectToRoute("app_login");
        	}        	        
        	else {
		        $errors = $form->getErrors(true, true);
		        $errorCollection = array();
		        foreach($errors as $error){
		               $errorCollection[] = $error->getMessage();
		        }		        
		        return $this->render('register/index.html.twig',[
		        	"form" => $form->createView(),
		        	"errors"=>$errorCollection
		        ]);
		    }        	
        }

        return $this->render('register/index.html.twig',[
        	"form" => $form->createView(),
        	"errors" => array()
        ]);
    }
}
