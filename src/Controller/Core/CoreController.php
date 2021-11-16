<?php

namespace App\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;

class CoreController extends AbstractController
{
    public function homepage()
    {
        return $this->render('core/core/index.html.twig', [
        ]);
    }

    public function datatablesLang()
    {  
        $arr_trans = [
            "sProcessing" => 'Traitement en cours...',
            "sSearch" => "Rechercher&nbsp;:",
            "sLengthMenu" => 'Afficher _MENU_ &eacute;l&eacute;ments',
            "sInfo" => "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "sInfoEmpty" => "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
            "sInfoFiltered" => "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "sInfoPostFix" => "",
            "sLoadingRecords" => "Chargement en cours...",
            "sZeroRecords" => "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "sEmptyTable" => "Aucune donn&eacute;e disponible dans le tableau",
            "oPaginate"=> [
                "sFirst" => "<i class='fas fa-fast-backward text-primary'></i>",
                "sPrevious" => "<i class='fas fa-step-backward text-primary'></i>",
                "sNext" => "<i class='fas fa-step-forward text-primary'></i>",
                "sLast" => "<i class='fas fa-fast-forward text-primary'></i>"
            ],
            "oAria" => [
                "sSortAscending" => ": activer pour trier la colonne par ordre croissant",
                "sSortDescending" => ": activer pour trier la colonne par ordre d&eacute;croissant"
            ],
            "buttons" => [
                "pageLength" => [
                    "_" => "Afficher %d éléments",
                    "-1" => "Tout afficher"
                ],
                "copyTitle" => "Ajouté au presse-papiers",
                "copySuccess" => [
                    "_" => "%d lignes copiées",
                    "1" => "1 ligne copiée"
                ]
            ]
        ];
        
        return new JsonResponse($arr_trans);
    }

    public static function getQuarter($startDate)
    {
        $current_month = $startDate->format('m');
        $current_year = $startDate->format('Y');
        $interval = new \DateInterval('P1D');
        $interval->invert = 1;
        if($current_month>=1 && $current_month<=4)
        {
            $quarter = 1;
            $start_date = new \dateTime('1-January-'.$current_year);  
            $end_date = new \dateTime('1-May-'.$current_year);  
        }
        else  if($current_month>=5 && $current_month<=8)
        {
            $quarter = 2;
            $start_date = new \dateTime('1-May-'.$current_year);  
            $end_date = new \dateTime('1-September-'.$current_year);  
        }
        else  if($current_month>=9 && $current_month<=12)
        {
            $quarter = 3;
            $start_date = new \dateTime('1-September-'.$current_year);
            $end_date = new \dateTime('1-December-'.$current_year);  
        }
        $end_date->add($interval);

        return [ "startDate" => $start_date , "endDate" => $end_date, "quarter" => $quarter ];
    }
}
