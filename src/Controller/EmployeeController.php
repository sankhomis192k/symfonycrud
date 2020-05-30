<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class EmployeeController extends AbstractController
{
    /**
     * @Route("/employee", name="employee")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Employee::class);

		$employees = $repository->findAll();

        return $this->render('employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }
    /**
     * @Route("/employee/add", name="add_employee", methods="post")
     */
    public function create(Request $req)
    {
       
        $entityManager = $this->getDoctrine()->getManager();


		$employee = new Employee();
		$employee->setFullname($req->request->get('fullname'));
		$employee->setEmail($req->request->get('email'));
		$employee->setMobile($req->request->get('mobile'));

		// tell Doctrine you want to (eventually) save the Employee (no queries yet)
        $entityManager->persist($employee);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
        return $this->redirectToRoute("employee");
                
    }
    /**
     * @Route("/employee/edit/{id}", name="update_employee", methods="post")
     */
    public function update(Request $req,$id){

    	$entityManager = $this->getDoctrine()->getManager();
	    $employee = $entityManager->getRepository(Employee::class)->find($id);

	    if (!$employee) {
	        throw $this->createNotFoundException(
	            'No employee found for id '.$id
	        );
	    }

	    $employee->setFullname($req->request->get('fullname'));
	    $employee->setEmail($req->request->get('email'));
		$employee->setMobile($req->request->get('mobile'));

	    $entityManager->flush();

	    return $this->redirectToRoute('employee');

    }

    /**
     * @Route("/employee/delete/{id}", name="delete_employee", methods="post")
     */
    public function delete($id){

    	$entityManager = $this->getDoctrine()->getManager();
	    $employee = $entityManager->getRepository(Employee::class)->find($id);

	    if (!$employee) {
	        throw $this->createNotFoundException(
	            'No employee found for id '.$id
	        );
	    }

	    $entityManager->remove($employee);
	    $entityManager->flush();

	    return $this->redirectToRoute('employee');

    }
}

