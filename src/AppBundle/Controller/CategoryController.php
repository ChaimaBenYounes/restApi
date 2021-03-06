<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Create a new category.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function newAction(Request $request)
    { 
        // GET the category parameters from the POST /categories/
        // we will trust that the input is safe
        $data = $request->getContent();
        
        $data = json_decode($data);
        
        if ($data == null) {
             $message = ['message' => 'An error occurred while processing your request.'];
            return new JsonResponse($message, 500);
        }

        if (!property_exists($data, 'name')) {
            $message = ['message' => 'The parameter `name` should be specified.'];
            return new JsonResponse($message, 422);
        }
               
        // Create a new empty object
        $category = new Category();
        
        // Use methods from the Category entity to set the values
        $category->setName($data->name);
        
        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Add our category to Doctrine so that it can be saved
        $em->persist($category);

        // Save our category
        $em->flush();
        
        // Return a success message.
        $message = ['message' => 'the category `'.$category->getName().'` has been saved .'];
        return new JsonResponse($message, 201);
        
    } 
  
    /**
     * Retrieve all data from database.
     * 
     * @return JsonResponse
     */
    public function listAction() 
    {
        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findAll();

        if ($categories == null) {
            $message = ['message' => 'There is no data in the database'];
            return new JsonResponse($message, 200);
        }
           
        $result = [];
        foreach($categories as $category) {
            $result [] = ['id' => $category->getId(), 'name' => $category->getName()];
        }

        return new JsonResponse($result);
    }
    
}
