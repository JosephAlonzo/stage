<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\Core\CoreController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Utils\DocumentTCPDF;
use App\Service\imageCreator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Security\Core\Security;

use App\Entity\Sport\OrientationSheet;
use App\Form\Sport\OrientationSheetType;
use App\Entity\Sport\AttendanceSheet;
use App\Entity\Sport\OrientationSheetPlannings;
use App\Entity\Sport\Beneficiary;
use App\Entity\Sport\Activity;
use App\Entity\Sport\Educator;
use App\Entity\Sport\Planning;
use Doctrine\Common\Collections\ArrayCollection;

class OrientationSheetController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    public function orientationSheetsList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('orientation_sheets_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.orientation_sheet.plural'),
            'current' => true
        ];

        return $this->render('sport/orientation_sheet/orientationSheetsList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function orientationSheetsListDatatables(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(OrientationSheet::class);
        $user = $this->getUser();

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
        $otherConditions = "o.tenant = " . $user->getTenant()->getId();

        if( $user->getSocialWorker() ){
            $otherConditions .= "o.socialWorker = " . $user->getSocialWorker()->getId();
        }
        else {
            if ( $this->isGranted('ROLE_ADVENSYS') ){
                $otherConditions .= null;
            }
            else{
                die;
            }
        }
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
                    case 'startDate':
                        $responseTemp = $element->getStartDate()->format("Y/m/d");
                    break;
                    case 'situation':
                        $responseTemp = $element->getSituation();
                    break;
                    case 'axe':
                        $axes = "";
                        $listAxes = $element->getAxes();
                        foreach ( $listAxes as $key => $axe) {
                            $axes .=  $axe;
                            $axes .= (($key+1)%count($listAxes)) != 0 ?  ", " : "";
                        }
                        $responseTemp = $axes;
                    break;
                    case 'photoAuthorization':
                        $authorization = $element->getPhotoAuthorization();
                        if($authorization){
                            $responseTemp = "<div class='text-success'>Oui</div>";
                        }
                        else{
                            $responseTemp = "<div class='text-danger'>Non</div>";
                        }
                    break;
                    case 'sendingDate':
                        $responseTemp = $element->getSendingDate()->format("Y/m/d");
                    break;
                    case 'confirmed':
                        $responseTemp = "";
                        $orientationSheetPlannigs = $em->getRepository(OrientationSheetPlannings::class)->findBy(
                            ['orientationSheet' => $element ]
                        );
                        foreach ($orientationSheetPlannigs as $key => $planning) {
                            $id = $planning->getId();
                            $confirmed = $planning->getConfirmed();
                            if( $confirmed ){
                                $responseTemp .= "<div class='form-check'> <input type='checkbox' class='form-check-input' id='{$key}' data-id='{$id}' checked> <label class='form-check-label' for='{$key}'>" . $planning->getPlanning()->getActivity()->getName() . "</label></div>";
                            }
                            else{
                                $responseTemp .= "<div class='form-check'> <input type='checkbox' class='form-check-input' id='{$key}' data-id='{$id}'> <label class='form-check-label' for='{$key}'>" . $planning->getPlanning()->getActivity()->getName() . "</label></div>";
                            }
                        }
                        
                    break;
                    case 'actions':
                        $confirmed = false;
                        foreach ($orientationSheetPlannigs as $key => $planning) {
                            $confirmed = $planning->getConfirmed();
                            if($confirmed){
                                break;
                            }
                        }
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_orientation_sheet', array('id' => $id));
                            $responseTemp = "<div class='row d-flex align-items-center flex-nowrap'> <a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_orientation_sheet', array('id' => $id));
                            $responseTemp .= "&nbsp;<a href='".$url."'><i class='mdi mdi-pencil-box-outline'></i></a>";
                            // $url = $this->generateUrl('send_confirmation_mail_orientation_sheet', array('id' => $id));
                            // $responseTemp .= "&nbsp;<a href='".$url."'  data-modal='modal' data-target-modal='#confrimModal' class='confirm' ><i class='fas fa-mail-bulk'></i></a> </div>";
                            if(!$confirmed){
                                $responseTemp .= "&nbsp;<a href='#' data-modal='modal' data-id='{$id}' data-target-modal='#confrimModal' class='confirm' ><i class='fas fa-mail-bulk'></i></a> </div>";                                
                            }
                            else{
                                $responseTemp .= "&nbsp;<a><i class='fas fa-mail-bulk'></i></a> </div>";                                
                            }
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

    public function sendConfirmationMailOrientationSheet(Request $request, OrientationSheet $orientationSheet, \Swift_Mailer $mailer, TranslatorInterface $translator, \Symfony\Component\Asset\Packages $assetsManager){
        $finder = new Finder();
        $em = $this->getDoctrine()->getManager();
        $activities = $request->request->get('activities');   
        foreach ($activities as $key => $activity) {
            $confirmed = $activity['confirmed'] == "true" ? true : false;
            $orientationSheetPlannigs = $em->getRepository(OrientationSheetPlannings::class)->find($activity['id']);
            $activitiesConfirmed = $em->getRepository(OrientationSheetPlannings::class)->findByConfirmedActivity($orientationSheetPlannigs->getPlanning()->getId());
            if( count( $activitiesConfirmed ) >= $orientationSheetPlannigs->getPlanning()->getMaxPlaces() ){
                continue;
            }
            $orientationSheetPlannigs->setConfirmed($confirmed);
            $em->persist($orientationSheetPlannigs);
            $em->flush();   
        }
        
        if ( $orientationSheetPlannigs->getConfirmed() ){
            $this->confirmationPdf($orientationSheet, $assetsManager, $finder, $translator);

            $message = (new \Swift_Message('Confirmation inscription tremplin'))
            ->setFrom('tremplin@example.com')
            ->setTo($orientationSheet->getBeneficiary()->getEmail())
            ->setSubject($translator->trans('email.subject.confirmation'))
            ->setBody(
                $this->renderView(
                    'emails/confirmation.html.twig',
                    ['orientationSheet' => $orientationSheet]
                ),
                'text/html'
            )
            ->attach( \Swift_Attachment::fromPath($this->getParameter('uploads_directory'). '/confirmation/confirmation.pdf'));
    
            $mailer->send($message);
    
            $orientationSheet->setSendingDate( new \DateTime('now') );
            $em->persist($orientationSheet);
            $em->flush();
    
            $this->addFlash('success', $translator->trans('messages.sended_mail') );
            
            $response = new JsonResponse(['success' => true]);
            return $response;
        }
        else{
            $this->addFlash('warning', $translator->trans('messages.failed_send_mail') );
            $response = new JsonResponse(['success' => false]);
            return $response;
        }
        
    }

    public function confirmationPdf(OrientationSheet $orientationSheet, \Symfony\Component\Asset\Packages $assetsManager, \Symfony\Component\Finder\Finder $finder, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();

        $options = [];
        $options['logo_path'] = realpath($this->getParameter('logos_directory').'/logo.jpg');

        $options['companyApp'] = $orientationSheet;
        $options['doc_name'] = "test pdf";
        $options['imageFooter'] = realpath($this->getParameter('logos_directory').'/footer_logo.jpg');

        $pdf = new DocumentTCPDF($options);

        $pdf->SetTitle('confirmation');
        $pdf->SetSubject('confirmation SetSubject');

        // remove default header/footer
        $pdf->setPrintFooter(true);

        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        $pdf->SetY(0);

        //Init pdf render
        $educators = $em->getRepository(Educator::class)->findAll();
        $result = CoreController::getQuarter($orientationSheet->getStartDate() );
        $user = $this->security->getUser();
        $planning = $em->getRepository(Planning::class)->findBetweenDates($result['startDate'] , $result['endDate'],  $user->getTenant()->getId() );
        //Create image to the 2nd 
        $this->CreateImage($orientationSheet, $translator, $planning, $result );

        $finder->files()->in( realpath( $this->getParameter('templates_directory')).'\pdf\confirmation' )->name('page*');
        foreach ($finder as $file) {
            
            $pdf->AddPage();
            $html =  $this->renderView(
                'pdf/confirmation/' . $file->getFilename(),
                [
                    'orientationSheet' => $orientationSheet,
                    'educators' => $educators, 
                    'plannings' => $planning, 
                    'quarter' => $result
                ]
            );
            $pdf->writeHTML($html, true, false, true, false, '');
        }
        
        $pdf->Output($this->getParameter('uploads_directory'). '/confirmation/confirmation.pdf', 'F');
    }

    public function pdfTest($id, $id2, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $orientationSheet = $em->getRepository(OrientationSheet::class)->find($id2);

        $educators = $em->getRepository(Educator::class)->findAll();
        $result = CoreController::getQuarter($orientationSheet->getStartDate() );
        $user = $this->security->getUser();
        $planning = $em->getRepository(Planning::class)
                    ->findBetweenDates($result['startDate'] , $result['endDate'],  $user->getTenant()->getId());
        $this->CreateImage($orientationSheet, $translator, $planning, $result );


        return $this->render('pdf/confirmation/page'.$id.'.html.twig', ['test' => true, 'orientationSheet' => $orientationSheet, 'educators' => $educators, 'plannings' => $planning, 'quarter' => $result ] );
    }

    public function CreateImage(OrientationSheet $orientationSheet, TranslatorInterface $translator, $plannings, $quarter){
        $daysOfWeek = ["", 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        $header = strtoupper($translator->trans("months." .  strtolower($quarter['startDate']->format("F"))  ) ) . " - " .
                strtoupper($translator->trans("months." .  strtolower($quarter['endDate']->format("F"))  ) ) . " " .
                $quarter['startDate']->format("Y");
        
        $footer = 'TREMPLIN SPORT - CDOS ' . $orientationSheet->getTenant()->getCdosNumber() . " - " .
                $orientationSheet->getTenant()->getAddress() . " - " . $orientationSheet->getTenant()->getCity()->getPostalCode() . " " . strtoupper ($orientationSheet->getTenant()->getCity()->getName()) . " - tél: " .
                $orientationSheet->getTenant()->getPhoneNumber() . " " . $orientationSheet->getTenant()->getEmail() . " - " . $orientationSheet->getTenant()->getSiteInternet();

        $table = [
            'header' => [ $header ],
            'columns'=> [],
            'footer' => [$footer],
            'options' => [
                'background' => [
                    'columns'=> [
                        'uploads/planning/tr-blue.jpg',
                        'uploads/planning/tr-orange.jpg'
                    ],
                    'header' => ['uploads/planning/header.jpg',],
                    'footer' => ['uploads/planning/footer.jpg']
                ],
                'images' => [
                    'arrow' => 'uploads/planning/arrow.jpg',
                    'new' => 'uploads/planning/new.png',
                    'division' => 'uploads/planning/division.png',
                ]
            ]
        ];
        
        foreach ($plannings as $key => $planning) {
            if(!$key){
                $currentDay = $planning->getDay();
            }
            $column[0] = $translator->trans('days.'. $daysOfWeek[$planning->getDay()]) . "\n" . 
                        ($planning->getBeginningTime()->format("i") == "00" ? $planning->getBeginningTime()->format("H\\h") :  $planning->getBeginningTime()->format("H\\hi") ) .  " - " . 
                        ($planning->getEndingTime()->format("i") == "00" ? $planning->getEndingTime()->format("H\\h") :  $planning->getBeginningTime()->format("H\\hi") ) ;
            $column[1] = $planning->getActivity()->getName() . " - " . $planning->getPlace()->getName() . "\n" . $planning->getPlace()->getAddress() . " - " . strtoupper ($planning->getPlace()->getCity()->getName() );
            $column[2] = 'à partir du' . "\n" . $planning->getStartDate()->format("d") . " " . $translator->trans('months.' . strtolower ($planning->getStartDate()->format("F") ) ) . "\n" . $planning->getNumberSessions() . " Séances" ;
            foreach ( $orientationSheet->getPlannings() as $activitySelected ){
                if($activitySelected->getPlanning()->getId() == $planning->getId()){
                    $column[3] = $activitySelected->getConfirmed();
                    break;
                }
                else{
                    $column[3] = false;
                }
            }
            //this column is for add the new label
            $column[4] = $orientationSheet->getCreatedAt();
            //this column is for add the division image
            $column[5] = $currentDay != $planning->getDay() ? true : false; 
            $currentDay = $planning->getDay();
            array_push($table['columns'], $column);
        }
        if( !count($table['columns']) ){
            return "add planning activities";
        }
        $font = realpath($this->getParameter('fonts_directory').'/Roboto/Roboto-Regular.ttf');
        $imageCreator = new imageCreator( $font, null, null );
        $imageCreator->createImageTable( $table );
    }

    public function editOrientationSheet(Request $request, $id, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('orientation_sheets_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.orientation_sheet.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $orientationSheet = new OrientationSheet();  
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_orientation_sheet', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.orientation_sheet.article') . " " . $translator->trans('entity.orientation_sheet.singular'),
                'current' => true
            ];
            $form = $this->createForm(OrientationSheetType::class, $orientationSheet);

        }
        else
        {
            $orientationSheet = $em->getRepository(OrientationSheet::class)->find($id);
            $orientationSheetPlannigs = $em->getRepository(OrientationSheetPlannings::class)->findBy(
                ['orientationSheet' => $orientationSheet ]
            );

            $attendanceSheets = $em->getRepository(AttendanceSheet::class)->findBy(
                ['orientationSheetPlanning' => $orientationSheetPlannigs ]
            );
            
            $msgFlashSuccess = 'messages.data_updated';
            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_orientation_sheet', ['id' => $orientationSheet->getId()]),
                'name' => $translator->trans('entity.orientation_sheet.singular').': '.$orientationSheet->getBeneficiary()->getName(),
                'current' => true
            ];
            $form = $this->createForm(OrientationSheetType::class, $orientationSheet);
            $collection = new ArrayCollection();
            $formPlannigField = $form->get("planning");
            foreach ($orientationSheetPlannigs as $key => $planning) {
                $collection[$key] = $planning->getPlanning();
            }
            $formPlannigField->setData($collection);
        }
        
     	$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $orientationSheet->setTenant( $user->getTenant() );
            $orientationSheet->getBeneficiary()->setTenant( $user->getTenant() );
            $attendanceSheetsArray = [];
            $plannings = $form->get("planning")->getData();
            if($id == 'new'){
                $attendances = [];
                
                foreach ( $plannings as $key => $planning) {
                    //added relation orientationSheet with plannigs
                    $orientationSheetPlannigs = new OrientationSheetPlannings();  
                    $orientationSheetPlannigs->setOrientationSheet($orientationSheet);
                    $orientationSheetPlannigs->setPlanning($planning);
                    $em->persist($orientationSheetPlannigs);
                    $em->flush();

                    $attendanceSheet = new AttendanceSheet();   
                    $this->updateAttendanceSheet($planning, $attendanceSheet, $orientationSheetPlannigs, $attendances, $user );

                    $orientationSheet->addPlanning($orientationSheetPlannigs);
                }
                
            }
            else{
                //Search in plannings to update
                foreach ( $plannings as $key => $planning) {
                    $attendanceSheetUpdate = new AttendanceSheet();   
                    $attendances = [];
                    $add = true;

                    $test = $orientationSheet->getPlannings();
                    foreach ( $test as $i => $value) {
                        if ( $planning->getId() == $value->getPlanning()->getId())
                        {
                           $add = false;
                           break;
                        }
                    }
                    if($add)
                    {
                        $orientationSheetPlannigs = new OrientationSheetPlannings();  
                        $orientationSheetPlannigs->setOrientationSheet($orientationSheet);
                        $orientationSheetPlannigs->setPlanning($planning);
                        $em->persist($orientationSheetPlannigs);
                        $em->flush();

                        $attendanceSheet = new AttendanceSheet();   
                        $this->updateAttendanceSheet($planning, $attendanceSheet, $orientationSheetPlannigs, $attendances, $user );
                        break;
                    }
                
                }
                
                foreach ( $attendanceSheets as $key => $attendanceSheet) {
                    $delete = true;
                    foreach ($plannings as $i => $planning) {
                        if ( $attendanceSheet->getOrientationSheetPlanning()->getPlanning()->getId() == $planning->getId()){
                            $delete = false;
                            break;
                        }
                    }
                    if($delete){
                        $this->deleteOrientationSheetPlanning($attendanceSheet->getOrientationSheetPlanning());
                        $this->deleteAttendanceSheet($attendanceSheet);
                    }
                }
            }
            
            $em->persist($orientationSheet);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_orientation_sheet',  ['id' => $orientationSheet->getId()]);
        }


        return $this->render('sport/orientation_sheet/editOrientationSheet.html.twig', array(
            'form' => $form->createView(),
            'orientationSheet' => $orientationSheet,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function updateAttendanceSheet($planning, AttendanceSheet $attendanceSheetUpdate, OrientationSheetPlannings $orientationSheetPlannigs, $attendances, $user ){
        $em = $this->getDoctrine()->getManager();

        $cycle = CoreController::getQuarter($planning->getStartDate());
        $attendanceSheetUpdate->setTenant( $user->getTenant() );
        $attendanceSheetUpdate->setCycle( $cycle['quarter'] . " - " . $planning->getStartDate()->format('Y') );
        $attendanceSheetUpdate->setOrientationSheetPlanning($orientationSheetPlannigs);

        if($attendances == null){
            for ($i=0; $i < $planning->getNumberSessions(); $i++) { 
                $attendances[$i] = "";
            }
            $attendanceSheetUpdate->setAttendances( $attendances );
        }
        $em->persist($orientationSheetPlannigs );
        $em->persist($attendanceSheetUpdate );
        $em->flush();
    }

    public function deleteAttendanceSheet(AttendanceSheet $attendanceSheet)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($attendanceSheet);
        $em->flush();
    }

    public function deleteOrientationSheetPlanning(OrientationSheetPlannings $orientationSheetPlanning)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($orientationSheetPlanning);
        $em->flush();
    }

    public function deleteOrientationSheet(Request $request, OrientationSheet $orientationSheet, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($orientationSheet);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted') );
        return $this->redirectToRoute("orientation_sheets_list");
    }
}
