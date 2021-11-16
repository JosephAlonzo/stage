<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Security\User;
use App\Form\Security\UserType;
use App\Form\Security\UserEditType;
use App\Form\Security\UserEditPasswordType;

class SecurityController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function usersList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('users_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.user.plural'),
            'current' => true
        ];

        $em = $this->getDoctrine()->getManager();
        return $this->render('security/usersList.html.twig', ['breadcrumbs' => $breadcrumbs]);
    }

    public function usersListDatatables(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
    
        if ($request->getMethod() == 'POST')
        {
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');
        }
        else
            die;

    
        foreach ($orders as $key => $order)
        {
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }
    
        $user = $this->getUser();
        $otherConditions = "u.tenant = " . $user->getTenant()->getId();
        $params = [];

        
        $results = $repository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions, $params);
    
        $objects = $results["results"];
        $total_objects_count = $repository->countElement();
        $selected_objects_count = count($objects);
        $filtered_objects_count = $results["countResult"];
    
        $response = '{
            "draw": '.$draw.',
            "recordsTotal": '.$total_objects_count.',
            "recordsFiltered": '.$filtered_objects_count.',
            "data": [';
    
        $i = 0;
    
        foreach ($objects as $key => $element)
        {
            $response .= '["';
    
            $j = 0; 
            $nbColumn = count($columns);
            foreach ($columns as $key => $column)
            {
                $id = $element->getId();

                $responseTemp = "";
                switch($column['name'])
                {
                    case 'id':
                        $responseTemp = $element->getId();
                    break;
                    case 'username':
                        $responseTemp = $element->getUserName();
                    break;
                    case 'roles':
                        $responseTemp .= implode(",", $element->getRoles());
                    break;
                    case 'isEnabled':
                        if( $element->getIsEnabled() ){
                            $responseTemp = '<strong class="text-success">Actif</strong>';
                        }
                        else{
                            $responseTemp = '<strong class="text-danger">Inactif</strong>';
                        }
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            // $responseTemp = "";
                            // if( $element->getSocialWorker() )
                            // {
                            //     $responseTemp .= "<div class='row d-flex align-items-center flex-nowrap'>";
                            // }
                            // else
                            // {
                            $url = $this->generateUrl('delete_user', array('id' => $id));
                            $responseTemp .= "<div class='row d-flex align-items-center flex-nowrap'><a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            // }
                            $url = $this->generateUrl('edit_user', array('id' => $id));
                            $responseTemp .= "&nbsp;<a href='".$url."'><i class='mdi mdi-pencil-box-outline'></i></a>";
                            $url = $this->generateUrl('edit_user', array('id' => $id));
                            $responseTemp .= "&nbsp;<a href='".$url."/change'><i class='fas fa-key'></i></a></div>";
                        }
                    break;
                }
    
                // Add the found data to the json
                $response .= str_replace('"', '', $responseTemp);
    
                if(++$j !== $nbColumn)
                    $response .='","';
            }
    
            $response .= '"]';
    
            // Not on the last item
            if(++$i !== $selected_objects_count)
                $response .= ',';
        }
    
        $response .= ']}';
    
        $returnResponse = new JsonResponse();
        $returnResponse->setJson($response);
    
        return $returnResponse;
    }


    public function editUser(Request $request, $id, $password, TranslatorInterface $translator, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('users_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.user.plural'),
            'current' => false
        ];

        if($password == "change"){
            $user = $em->getRepository(User::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_user', ['id' => $user->getId()]),
                'name' => $translator->trans('entity.user.singular').': '.$user->getUserName(),
                'current' => true
            ];

            $form = $this->createForm(UserEditPasswordType::class, $user);
        }
        else{
            if ($id == 'new')
            {
                $user = new User();
                $msgFlashSuccess = 'messages.data_created';

                $breadcrumbs[] = [
                    'active' => 'active',
                    'href' => $this->generateUrl('edit_user', ['id' => $id]),
                    'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.user.article') . " " . $translator->trans('entity.user.singular'),
                    'current' => true
                ];
                $form = $this->createForm(UserType::class, $user);
            }
            else
            {
                $user = $em->getRepository(User::class)->find($id);
                $msgFlashSuccess = 'messages.data_updated';

                $breadcrumbs[] = [
                    'active' => 'active',
                    'href' => $this->generateUrl('edit_user', ['id' => $user->getId()]),
                    'name' => $translator->trans('entity.user.singular').': '.$user->getUserName(),
                    'current' => true
                ];

                $form = $this->createForm(UserEditType::class, $user);
            }
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(empty($user->getRoles())){
                $user->setRoles(['ROLE_USER']);
            }
            if($password == "change" || $id == "new" ){
                $passwordEncoded = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($passwordEncoded);
            }
            
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_user',  ['id' => $user->getId(), 'password' => $password]);
        }

        return $this->render('security/editUser.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id,
            'password' => $password
        ));
    }

    public function userDelete(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $enable = $user->getIsEnabled()? False: True;
        $user->setIsEnabled($enable);
        $em->flush();

        $this->addFlash('success', "Utilisateur (".$user->getUsername().") supprimé avec succès");
        return $this->redirectToRoute("users_list");
    }
}
