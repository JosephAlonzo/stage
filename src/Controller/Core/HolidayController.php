<?php

namespace App\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Core\Holiday;
use App\Form\Core\HolidayType;


class HolidayController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    
    public function holidaysList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('holidays_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.holiday.plural'),
            'current' => true
        ];

        return $this->render('core/holiday/holidaysList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function holidaysListDatatables(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Holiday::class);
    
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
    
        $user = $this->security->getUser();
        $otherConditions = "h.tenant = " . $user->getTenant()->getId();
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
                    case 'name':
                        $responseTemp = $element->getName();
                    break;
                    case 'startDate':
                        $responseTemp = $element->getStartDate()->format('d/m/y');
                    break;
                    case 'endDate':
                        $responseTemp = $element->getEndDate()->format('d/m/y');
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_holiday', array('id' => $id));
                            $responseTemp = "<a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_holiday', array('id' => $id));
                            $responseTemp .= "&nbsp;<a href='".$url."'><i class='mdi mdi-pencil-box-outline'></i></a>";
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


    public function editHoliday(Request $request, $id, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('holidays_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.holiday.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $holiday = new Holiday();            
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_holiday', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.holiday.article') . " " . $translator->trans('entity.holiday.singular'),
                'current' => true
            ];
        }
        else
        {
            $holiday = $em->getRepository(Holiday::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_holiday', ['id' => $holiday->getId()]),
                'name' => $translator->trans('entity.holiday.singular').': '.$holiday->getName(),
                'current' => true
            ];
        }
        
     	$form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $holiday->setTenant( $user->getTenant());

            $em->persist($holiday);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_holiday',  ['id' => $holiday->getId()]);
        }

        return $this->render('core/holiday/editHoliday.html.twig', array(
            'form' => $form->createView(),
            'holiday' => $holiday,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function deleteHoliday(Request $request, Holiday $holiday, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($holiday);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted'));
        return $this->redirectToRoute("holidays_list");
    }

    public function addApiHolidays(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Holiday::class);

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://data.education.gouv.fr'
        ]);
        $currentYear = new \dateTime('now');
        $currentYear = $currentYear->format('Y');
        $startYear = intval( $currentYear ) - 1;
        $endYear = $currentYear;

        $response = $client->request('GET', "api/records/1.0/search/?".
        "dataset=fr-en-calendrier-scolaire".
        "&q=&facet=description&facet=start_date&facet=end_date&facet=location&facet=annee_scolaire&facet=zones&".
        "refine.location=Nancy-Metz" .
        "&refine.annee_scolaire={$startYear}-{$endYear}"
        );
        $holidays = json_decode( $response->getBody()->getContents())->records;
        $formato = 'Y-m-d';
        
        foreach ($holidays as $key => $holidayApi) {
            $holidayApi = $holidayApi->fields;
            $response = $repository->findByDates($holidayApi->start_date, $holidayApi->end_date);
            if( count($response) > 0 ){
                continue;
            }
            $holiday = new Holiday(); 
            $user = $this->security->getUser();
            $holiday->setTenant( $user->getTenant());
            $holiday->setName( $holidayApi->description );
            $start = \DateTime::createFromFormat($formato, $holidayApi->start_date);
            $end = \DateTime::createFromFormat($formato, $holidayApi->end_date);
            $holiday->setStartDate( $start );
            $holiday->setEndDate( $end );

            $em->persist($holiday);
            $em->flush();
        }
        

        $msgFlashSuccess = 'messages.data_created';
        $this->addFlash('success', $msgFlashSuccess);

        return $this->holidaysList($request, $translator);
    }
}
