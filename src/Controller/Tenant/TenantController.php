<?php

namespace App\Controller\Tenant;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Tenant\Tenant;
use App\Form\Tenant\TenantType;


class TenantController extends AbstractController
{
    public function tenantsList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('tenants_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.tenant.plural'),
            'current' => true
        ];

        return $this->render('tenant/tenant/tenantsList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function tenantsListDatatables(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Tenant::class);
    
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
    
        $otherConditions = null;
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
                    case 'cdosName':
                        $responseTemp = $element->getCdosName();
                    break;
                    case 'cdosNumber':
                        $responseTemp = $element->getCdosNumber();
                    break;
                    case 'siret':
                        $responseTemp = $element->getSiret();
                    break;
                    case 'codeApe':
                        $responseTemp = $element->getCodeApe();
                    break;
                    case 'address':
                        $responseTemp = $element->getAddress();
                    break;
                    case 'city':
                        $responseTemp = $element->getCity()->getName();
                    break;
                    case 'phoneNumber':
                        $responseTemp = $element->getPhoneNumber();
                    break;
                    case 'email':
                        $responseTemp = $element->getEmail();
                    break;
                    case 'siteInternet':
                        $responseTemp = $element->getSiteInternet();
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_tenant', array('id' => $id));
                            $responseTemp = "<div class='row'><a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_tenant', array('id' => $id));
                            $responseTemp .= "&nbsp;<a href='".$url."'><i class='mdi mdi-pencil-box-outline'></i></a></div>";
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


    public function editTenant(Request $request, $id, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('tenants_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.tenant.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $tenant = new Tenant();            
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_tenant', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.tenant.article') . " " . $translator->trans('entity.tenant.singular'),
                'current' => true
            ];
        }
        else
        {
            $tenant = $em->getRepository(Tenant::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_tenant', ['id' => $tenant->getId()]),
                'name' => $translator->trans('entity.tenant.singular').': '.$tenant->getCdosName(),
                'current' => true
            ];
        }
        

     	$form = $this->createForm(TenantType::class, $tenant);

     	$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tenant);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_tenant',  ['id' => $tenant->getId()]);
        }

        return $this->render('tenant/tenant/editTenant.html.twig', array(
            'form' => $form->createView(),
            'tenant' => $tenant,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function deleteTenant(Request $request, Tenant $tenant, TranslatorInterface $translator)
    {
        if($tenant->hasRelations()){
            $this->addFlash('warning', $translator->trans('messages.failed_data_deleted_association') . " des éléments associés");
            return $this->redirectToRoute("tenants_list");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($tenant);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted'));
        return $this->redirectToRoute("tenants_list");
    }
}
