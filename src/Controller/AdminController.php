<?php

namespace App\Controller;

use App\Entity\User;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Knp\Component\Pager\PaginatorInterface;

class AdminController extends AbstractController
{

    /**
    * 
    * @Route("/admin", name="admin_list")
    */
    public function admin(Request $request ,PaginatorInterface $paginator)
    {
        
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $users = $paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        
        return $this->render('admin/index.html.twig', [
            'users' => $users
            
        ]);
    }
    /*public function add(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        # only admin function
    }

    // ...*/
     /**
     * @Route("/admin/Delete/{id}" ,name="DELETE_USER")
     *Method({"DELETE"})
     */
    public function Delete(Request $request,$id)
    {
            $User = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($User);
            $entityManager->flush();

            
            return $this->redirectToRoute('admin_list');
            
    }
       /**
     * @Route("/admin/update/{id}" ,name="BLOCK_USER")
     *Method({"GET", "POST"})
     */
    public function Block(Request $request,$id)
    {       
            $User = new User();
            $User = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

            $form = $this->createformbuilder($User)
            ->add('isExpired',CheckboxType::class, [
                'label'    => 'BLOCK',
                'required' => false,
            ])
            ->add('Done',SubmitType::class)
            ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() ) {
                 $entityManager = $this->getDoctrine()->getManager();
                 $entityManager->flush();

                return $this->redirectToRoute('admin_list');
            }
            return $this->render('admin/update.html.twig', [
                'form' => $form->createView()
               ]);
            
    }
    /**
     * @Route("/admin/filter" ,name="filter")
     */

    public function listwhereadminfirst(){
        $users=$this->getDoctrine()
                    ->getRepository(User::class)
                    ->findusers();
        return $this->render('admin/test.html.twig', [
                        'users' => $users
                    ]);
    }
    
    /**
    * 
    * @Route("/test", name="test")
    */
    public function adminn()
    {
        return $this->render('backtemplate/billing.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/admin/{id}", name="user_show", methods={"GET"} , requirements={"id":"\d+"})
     */
    public function show(User $users): Response
    {
        return $this->render('admin/show.html.twig', [
            'users' => $users,
        ]);
    }
   

    /**
   * Creates a new ActionItem entity.
   *
   * @Route("/search", name="ajax_search")
   * @Method("GET")
   */
  public function searchAction(Request $request)
  {
      $em = $this->getDoctrine()->getManager();

      $requestString = $request->get('q');

      $User =  $em->getRepository(User::class)->findEntitiesByString($requestString);

      if(!$User) {
          $result['User']['error'] = "Not Found";
      } else {
          $result['User'] = $this->getRealEntities($User);
      }

      return new Response(json_encode($result));
  }

  public function getRealEntities($User){

      foreach ($User as $User){
          $realEntities[$User->getId()] = [$User->getUsername(),$User->getImage()];
      }

      return $realEntities;
  }
   
}
