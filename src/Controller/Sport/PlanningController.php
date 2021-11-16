<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Security\Core\Security;

use App\Entity\Sport\Planning;
use App\Entity\Sport\Activity;
use App\Entity\Sport\Educator;
use App\Entity\Sport\Place;
use App\Entity\Core\Holiday;
use App\Form\Sport\PlanningType;

class PlanningController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security = null)
    {
       $this->security = $security;
    }

    public function planningsList(TranslatorInterface $translator): Response
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('plannings_list'),
            'name' => ucfirst( $translator->trans('entity.planning.singular') ),
            'current' => true
        ];

        return $this->render('sport/planning/planningsList.html.twig', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function editPlanning(Request $request, $id, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $holidays = $em->getRepository(Holiday::class);
        $breadcrumbs = [];
        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('plannings_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.planning.plural'),
            'current' => false
        ];
        $activityId = $request->request->get('activity');

        if ($id == 'new')
        {
            $planning = new Planning();
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_planning', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.planning.article') . " " . $translator->trans('entity.planning.singular'),
                'current' => true
            ];
        }
        else
        {
            $action = $request->get('action');
            $start = \DateTime::createFromFormat('Y-m-d', $request->get('start'));
            $planning = $em->getRepository(Planning::class)->find($id);

            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_planning', ['id' => $planning->getId()]),
                'name' => $translator->trans('entity.planning.singular').': '.$planning->getStartDate()->format('Y/m/d'),
                'current' => true
            ];
            if( $action == "drop" ){
                $planning->setStartDate($start);
                $planning->setDay($start->format('N'));
                $holidays = $holidays->findAfterDate($planning->getStartDate() );
                $planning->setEndDate( $this->getEndDate($planning, $holidays) );

                $isValid= $this->isValidPlanning($em, $planning, $holidays, $translator);
                if(!$isValid){
                    $returnResponse = new JsonResponse();
                    return $returnResponse->setJson(json_encode(['success' => false]));
                }

                $em->persist($planning);
                $em->flush();
                $returnResponse = new JsonResponse();
                return $returnResponse->setJson(json_encode(['success' => true]));
            }
        }

        if($activityId){
            $activity = $em->getRepository(Activity::class)->find($activityId);
            $planning->setActivity($activity);
            $planning->setMaxPlaces($activity->getMaxPlaces());
            $planning->setAjaxCall(true);
        }

        $form = $this->createForm(PlanningType::class, $planning);        
        $form->handleRequest($request); 

        if(isset($request->request->get('planning')['educator'])){
            $idEducator = $request->request->get('planning')['educator'];
            if($idEducator){
               $educator = $em->getRepository(Educator::class)->find($idEducator );
               $planning->setEducator( $educator );
           }
        }
   
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $planning->setTenant( $user->getTenant());
            
            $holidays = $holidays->findAfterDate($planning->getStartDate() );
            $planning->setEndDate( $this->getEndDate($planning, $holidays) );
            $isValid= $this->isValidPlanning($em, $planning, $holidays, $translator);

            if(!$isValid){
                return $this->render('sport/planning/editPlanning.html.twig', array(
                    'form' => $form->createView(),
                    'planning' => $planning,
                    'breadcrumbs' => $breadcrumbs,
                    'id' => $id
                ));
            }

            $attendanceSheets = $planning->getOrientationSheets();       
            if( count($attendanceSheets) > 0 ){
                foreach ($attendanceSheets as $key => $attendanceSheet) {
                    $attendanceSheet = $attendanceSheet->getAttendanceSheet();
                    $attendances = $attendanceSheet->getAttendances();
                    for ($i=0; $i < $planning->getNumberSessions(); $i++) { 
                        if(!isset($attendances[$i]) ){
                            $attendances[$i] = "";
                        }
                    }
                    $attendanceSheet->setAttendances( $attendances );
                    $em->persist($attendanceSheet);
                }
            }

            $em->persist($planning);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_planning',  ['id' => $planning->getId()]);
        }

        return $this->render('sport/planning/editPlanning.html.twig', array(
            'form' => $form->createView(),
            'planning' => $planning,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function fullCalendarEvents(Request $request, TranslatorInterface $translator){
        $em = $this->getDoctrine()->getManager();
        $user = $this->security->getUser();
        $repository = $em->getRepository(Planning::class);
        $startDate = new \DateTime($request->get('start'));
        $endDate = new \DateTime($request->get('end'));

        $tenantId =  $user->getTenant()->getId();
        $maxSession =  $repository->getMaxNumberSessions($startDate, true);
        $startDate->add( $this->getInterval($maxSession, true));

        $results  = $repository->findBetweenDates($startDate, $endDate, $tenantId);

        $holidays = $this->getHolidays($em, $startDate, $endDate);
        $events = [];
        $events += $holidays[1];
        
        $daysOfWeek = ["", 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($results as $index => $value) {
            $date = $value->getStartDate(); 
            $day  = $value->getDay();
            for ($i=0; $i < $value->getNumberSessions(); $i++) { 
                // validate if the start date for the sport session is the same as the asigned in the form 
                if( $i || $date->format('N') != $day || $this->compareHolidayToEventDate($holidays[0], $date) )
                {
                    $nextDay = "next " . $daysOfWeek[ $day ];
                    do {
                        $date->modify($nextDay);     
                    } while ( $this->compareHolidayToEventDate($holidays[0], $date) );
                }
                $event = [
                    'id'     => $value->getId(),
                    'resourceId' => $value->getPlace()->getId(),
                    'title'  => $value->getActivity()->getName(),
                    'start'  => $date->format('Y-m-d\T') . $value->getBeginningTime()->format('H:i:s'),
                    'end'    => $date->format('Y-m-d\T') . $value->getEndingTime()->format('H:i:s'),
                    'backgroundColor' => [$value->getActivity()->getColor()],
                    'allDay' => false,
                    'extendedProps' => [
                        'place' => $value->getPlace()->getName(),
                        'status'=> $value->getStatus(),
                        'holiday' => false,
                    ]
                ];
                array_push($events, $event);
            }
        }
        $returnResponse = new JsonResponse();
        $returnResponse->setJson(json_encode($events));
        return $returnResponse;
    }

    public function fullCalendarRessources(Request $request, TranslatorInterface $translator){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Place::class);
        $results = $repository->findAll();
        $ressources = [];

        foreach ($results as $index => $value) {
            $ressource = [
                'id'     => $value->getId(),
                'title'  => $value->getName()
            ];
            array_push($ressources, $ressource);
        }
        $returnResponse = new JsonResponse();
        $returnResponse->setJson(json_encode($ressources));
        return $returnResponse;
    }

    public function isValidPlanning($em, $planning, $holidays, $translator){
        //This fonction is for validate if the planning is not at the same place at the same date and the educator is in another activity
        $end = $this->getEndDate($planning, $holidays);
        $validation = $em->getRepository(Planning::class)->comparePlanningSessions($planning, $holidays, $end);
        if( !$validation['place'] || !$validation['educator']  ) {
            $msgFlashInvalid = $translator->trans('messages.validate_planning');
            $msgFlashInvalid2 = !$validation['place'] ? "\n" .$translator->trans('messages.invalid_place'): "";
            $msgFlashInvalid3 = !$validation['educator'] ? "\n " . $translator->trans('messages.invalid_educator'): "";
            
            $this->addFlash('warning', $msgFlashInvalid . $msgFlashInvalid2 .  $msgFlashInvalid3);
           
           return false;
        }
        return true;
    }

    function getEndDate($planning, $holidays){
        
        $startDate = $planning->getStartDate();
        $day  = $planning->getDay();
        $date = clone $startDate;

        $daysOfWeek = ["", 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        for ($i=0; $i < $planning->getNumberSessions(); $i++) { 
            if( $i || $date->format('N') != $day || $this->compareHolidayToEventDate($holidays, $date) )
            {
                $nextDay = "next " . $daysOfWeek[ $day ];
                do {
                    $date->modify($nextDay); 
                } while ( $this->compareHolidayToEventDate($holidays, $date) );
            }
            
            $end = $date;
        }
        return $end;
    }

    function getHolidays($em, $startDate, $endDate){
        $repository = $em->getRepository(Holiday::class);
        $results = $repository->findBetweenDates($startDate,  $endDate);
        $holidays = [];
        foreach ($results as $index => $value) {
            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($value->getStartDate(), $interval, $value->getEndDate()->add( new \DateInterval('P1D') ));
            foreach ($period as $dt) {
                $holiday = [
                    'start'  => $dt->format("Y-m-d"),
                    'end'  => $dt->format("Y-m-d"),
                    'allDay' => true,
                    'extendedProps' => [
                        'holiday' => true,
                    ]
                ];
                array_push($holidays, $holiday);
            }
        }
        return [$results , $holidays];
    }

    function getInterval($maxSession, $negative = false){
        $maxSession ++;
        $interval = new \DateInterval('P' . (7 * $maxSession) . 'D');
        $negative ? $interval->invert = 1: "";
        return $interval;
    }

    function compareHolidayToEventDate($holidays, $eventDate){
        foreach ($holidays as $key => $holiday) {
            if($holiday->getStartDate() <= $eventDate && $holiday->getEndDate() >= $eventDate){
                return true;
            }
        }
        return false;
    }

    public function deletePlanning(Request $request, Planning $planning, TranslatorInterface $translator)
    {
        if( count($planning->getOrientationSheets()) > 0 ){
            $this->addFlash('warning', $translator->trans('messages.failed_data_deleted_association') . " des fiches d'orientation associés");
            return $this->redirectToRoute("plannings_list");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($planning);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted'));
        return $this->redirectToRoute("plannings_list");
    }
}