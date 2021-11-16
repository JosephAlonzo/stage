<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Sport\AttendanceSheet;
use App\Entity\Sport\Beneficiary;
use Symfony\Component\Security\Core\Security;

class AttendanceSheetController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    public function attendanceSheetsList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('attendance_sheets_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.attendance_sheet.plural'),
            'current' => true
        ];

        return $this->render('sport/attendance_sheet/attendanceSheetsList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function attendanceSheetsListDatatables(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(AttendanceSheet::class);
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
        $otherConditions = "a.tenant = " . $user->getTenant()->getId();

        $groupBy = 'p.id';

        $otherConditions .= " AND op.confirmed = 1";
        $params = [];
        
        $results = $repository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions, $params, $groupBy);
    
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
                    case 'planning':
                        $responseTemp = $element->getOrientationSheetPlanning()->getPlanning()->getId();
                    break;
                    case 'activity':
                        $responseTemp = $element->getOrientationSheetPlanning()->getPlanning()->getActivity()->getName();
                    break;
                    case 'startDate':
                        $responseTemp = $element->getOrientationSheetPlanning()->getPlanning()->getStartDate()->format("Y/m/d");
                    break;
                    case 'cycle':
                        $responseTemp = $element->getCycle();
                    break;
                    case 'numberSessions':
                        $responseTemp = $element->getOrientationSheetPlanning()->getPlanning()->getNumberSessions();
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                           $responseTemp = "&nbsp;<a href='#table' id='updateTable' ><i class='fas fa-eye'></i></a>";
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

    public function attendanceSheetsListDatatablesDetails(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(AttendanceSheet::class);
        $user = $this->getUser();

        if ($request->getMethod() == 'POST')
        {
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');
            $planningId = $request->request->get('planningId');
        }
        else
            die;
    
        foreach ($orders as $key => $order)
        {
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }

        $user = $this->security->getUser();
        $otherConditions = "a.tenant = " . $user->getTenant()->getId();
        $otherConditions .= " and op.planning = " . $planningId;
        $otherConditions .= " and op.confirmed = 1";
        
        $groupBy = 'o.beneficiary';
        $params = [];

        $results = $repository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions, $params, $groupBy);
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
                    case 'lastName':
                        $responseTemp = $element->getOrientationSheetPlanning()->getOrientationSheet()->getBeneficiary()->getLastName();
                    break;
                    case 'firstName':
                        $responseTemp = $element->getOrientationSheetPlanning()->getOrientationSheet()->getBeneficiary()->getName();
                    break; 
                    case 'axe':
                        $axes = $element->getOrientationSheetPlanning()->getOrientationSheet()->getAxes();
                        $responseTemp = "";
                        foreach ($axes as $key => $axe) {
                            $responseTemp.= $key != (count($axes) - 1) ? $axe . "," :  $axe ;
                        }
                    break;
                    default:
                        try {
                            $value = $element->getAttendances()[$key-3];
                            $checked = "";
                            if($value == "x"){
                                $checked = " checked=\"checked\" ";
                            }
                            else{
                                $checked = null;
                            }
                            $index = $key-3;
                            $id = $element->getId();
                            $idP = rand(10000,99999);
                            $idSwitch = rand(10000,99999);
                            $responseTemp = "<div class='custom-control custom-switch'><input type=\"checkbox\" data-index=\"{$index}\" data-id=\"{$id}\" data-idP=\"{$idP}\" class='custom-control-input attendances' id=\"{$idSwitch}\" {$checked}><label class='switch-custom custom-control-label' for=\"{$idSwitch}\"></label></div>";
                            $responseTemp .= "<p class='d-none' id='{$idP}'>" . $value . "</p>";
                        } catch (\Throwable $th) {
                            dd($element); 
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

    public function editAttendanceSheet(Request $request, $id): Response
    {
        if ($request->getMethod() == 'POST')
        {
            $index = $request->request->get('index');
            $value = $request->request->get('value');
        }
        else
            die;

        $em = $this->getDoctrine()->getManager();
        $attendaceSheet = $em->getRepository(attendanceSheet::class)->find($id);
        $attendances = $attendaceSheet->getAttendances();
        $attendances[$index] = $value;
        $attendaceSheet->setAttendances($attendances);

        $em->persist($attendaceSheet);
        $em->flush();

        return $this->json(['success' => true ]);
    }

    public function getDataChart(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(AttendanceSheet::class);
        $beneficiaryRepository = $em->getRepository(Beneficiary::class);
        
        if ($request->getMethod() == 'POST')
        {
            $start = $request->request->get('start');
            $end = $request->request->get('end');
        }
        $user = $this->getUser();
        $tenant = $user->getTenant()->getId(); 

        if($start != null && $end != null){
            $formato = 'd/m/Y';
            $start = \DateTime::createFromFormat($formato, $start);
            $end = \DateTime::createFromFormat($formato, $end);

            $results = $repository->getAll($tenant, 'a.id', $start, $end);
        }
        else{
            $results = $repository->getAll($tenant, 'a.id');
        }
        $accumulatedPercentage = 0;
        $repeatedActivity = 0;
        $activities    = [];
        $sessions      = [];
        $rows          = [];
        $rowsTable     = [];
        
        foreach ($results as $key => $element) {
            $activity = [
                "id" => $element->getOrientationSheetPlanning()->getPlanning()->getId(),
                "name" => $element->getOrientationSheetPlanning()->getPlanning()->getActivity()->getName(),
                "startDate" => $element->getOrientationSheetPlanning()->getPlanning()->getStartDate()->format("Y/m/d"),
                "attendances" => $element->getAttendances(),
                "color" => $element->getOrientationSheetPlanning()->getPlanning()->getActivity()->getColor(),
                "sessions" => $element->getOrientationSheetPlanning()->getPlanning()->getNumberSessions(),
            ];
            array_push( $activities, $activity );
        }
        foreach ($activities as $i => $activity) {
            if( !$i ){
                $idTemp = $activity['id'];
            }
            if( $idTemp == $activity['id'] ){
                $attendances = $activity['attendances'];
                $percentageForAttendance = 100 / $activity['sessions'];
                $repeatedActivity += 1;
                foreach ($attendances as $j => $attendance) {
                    if( !isset($sessions["S". ($j+1) ]) ) {
                        $sessions["S". ($j+1) ]= 0;
                    }
                    if ($attendance) {
                        $sessions["S". ($j+1) ] += 1;
                        $accumulatedPercentage += 1 * $percentageForAttendance;
                    }
                }
            }
            
            $index = isset( $activities[$i+1] ) ? $i+1: 0;
            if( $idTemp != $activities[$index]['id'] || $index === 0 ) {
                array_push($rows, $activity);
                $rows[ count($rows) - 1 ]['attendances'] = $sessions;
                $rows[ count($rows) - 1 ]['percentage'] =  number_format($accumulatedPercentage / $repeatedActivity, 2, '.', '');
                $idTemp = $activities[$index]['id'];
                $accumulatedPercentage = 0;
                $repeatedActivity = 0;
                $sessions = [];
            }

        }

        if( $id == "general" ){
            $cols = [
                ['id'=> 'Id', 'label'=> '', 'type'=> 'number'],
                ['id'=> 'Activity', 'label'=> 'Activity', 'type'=> 'string'],
                ['id'=> 'percent', 'label'=> 'Pourcentage de présence', 'type'=> 'number'],
                ['id'=> '', 'type'=> 'string', 'role'=> 'style'  ],
                ['id'=> '', 'type'=> 'string', 'role'=> 'annotation'  ]
            ];
            foreach ($rows as $key => $row) {
                $tmpRow = 
                    ['c'=>[
                        ['v'=> $row['id'] ], 
                        ['v'=> $row['name'] . " " .  $row['startDate'] ], 
                        ['v'=> $row['percentage'] ],
                        ['v' => $row['color'] ],
                        ['v' => "Séances: " . $row['sessions'] ],
                    ]];
                array_push($rowsTable, $tmpRow);
            }
            $data = [ 'cols'=> $cols, 'rows'=> $rowsTable ];
        }
        else if($id == "gender"){
            $rows = $beneficiaryRepository->countBeneficiaryByGender($tenant);
            $cols = [
                ['id'=> 'gender', 'label'=> 'Sexe', 'type'=> 'string'],
                ['id'=> 'percent', 'label'=> 'Pourcentage', 'type'=> 'number'],
                ['id'=> 'style', 'type'=> 'string', 'role'=> 'style'  ]
            ];
            $rowsTable = [
                ['c'=>[
                    ['v'=> "Masculine" ], 
                    ['v'=> ($rows[0]['male'] / $rows[0]['total']) * 100 ],
                    ['v' => "#60C0FC" ]
                    ]
                ],
                ['c'=>[
                    ['v'=> "Féminin"], 
                    ['v'=> ($rows[0]['female'] /  $rows[0]['total']) * 100  ],
                    ['v' => "#FC60AB" ]
                    ]
                ]
            ];

            $data = [ 'cols'=> $cols, 'rows'=> $rowsTable ];
        }
        else if ($id == "pyramideMasculine" || $id == "pyramideFeminine"){
            $rows = $beneficiaryRepository->beneficiaryMasculine($tenant);
            $totalBeneficiaries = $beneficiaryRepository->countBeneficiaries($tenant);
            $totalBeneficiaries = $totalBeneficiaries[0]['total'];
            foreach ($rows as $key => $row) {
                if($id == "pyramideMasculine"){
                    if($row["gender"] == "M"){
                        $rows = array_reverse($row);
                        break;
                    }
                }
                else{
                    if($row["gender"] == "F"){
                        $rows = array_reverse($row);
                        break;
                    }
                }
            }
            $cols = [
                ['id'=> 'Id', 'label'=> '', 'type'=> 'string'],
                ['id'=> 'age', 'label'=> 'âge', 'type'=> 'number'],
            ];
            foreach ($rows as $key => $row) {
                if($key == "gender"){
                    continue;
                }
                else{
                    $key = explode("_", substr($key, 1 ));
                }
                $tmpRow = 
                    ['c'=>
                        [
                            ['v'=> $key[0] . "-" . $key[1] ], 
                            ['v'=> $row ]
                        ]
                    ];
                array_push($rowsTable, $tmpRow);
            }
            $data = [ 'cols'=> $cols, 'rows'=> $rowsTable ];
            return $this->json([ "data" => $data, "total" => $totalBeneficiaries]);
        }
        else{
            $maxBeneficiaries = $repository->countBeneficiary($id);
            $percentageForBeneficiary = 100 / $maxBeneficiaries;

            $activity = "";
            $cols = [
                ['id'=> 'Session', 'label'=> 'Séances', 'type'=> 'string'],
                ['id'=> 'Assitence', 'label'=> 'Pourcentage de présence', 'type'=> 'number'],
                ['id'=> '', 'type'=> 'string', 'role'=> 'style'  ],
                ['id'=> '', 'type'=> 'string', 'role'=> 'annotation'  ]
            ];
            foreach ($rows as $i => $row) {
                if($row['id'] == $id){
                    $attendances = $row['attendances'];
                    $activity = $row['name'];
                    foreach ($attendances as $j => $attendance) {
                        $tmpRow = 
                        ['c'=>[
                            ['v'=> $j ],
                            ['v'=> $attendance * $percentageForBeneficiary ],
                            ['v'=> $row['color'] ],
                            ['v'=> $attendance ],
                        ]];
                        array_push($rowsTable, $tmpRow);
                    }
                    break;
                }
            }
            $table = [ 'cols'=> $cols, 'rows'=> $rowsTable ];
            $data = [ 'table'=> $table, 'info' => [ 'activity'=> $activity  ] ];
        }
        
        return $this->json($data);
    }
   
}
