<?php
namespace Drupal\vdot_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformSubmissionForm;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\webform\Entity;
use Drupal\webform\Entity\WebformSubmission;

class CustomModController extends ControllerBase{
    
    public function import_webform_csv(){
        
        $filePath = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");
        $filename = $filePath.'/current.csv';
        $handle = fopen($filename, 'r');
        //kint($handle);
        $arrSubmission = array();
        while (($rowData = fgetcsv($handle)) !== FALSE) {
            //kint($rowData);
            $response = $this->add_rsvp_submission($rowData);
            //kint($response);
            break;
        }
        fclose($handle);

        $response = $response;
        return new Response(render($response));
    
    }
    
    public function add_rsvp_submission($rowData = array()){
        
        foreach($rowData as $data){
            $wedformID[] = $data;
        }
        //add to webform and send edm
        $responseWF = false;
        $response = array();
        $values = null;
        //Get the User ID
        $name = $wedformID[1];
        $users = \Drupal::entityTypeManager()->getStorage('user')
          ->loadByProperties(['name' => $name]);
        $user = reset($users);
        if ($user) {
          $uid = $user->id();
          $rids = $user->getRoles();
        }
        else{
        echo "No UID for " . $name;
        }
        
        $values = [
            'webform_id' => '2018_vdot_survey',
            'entity_type' => 'node',
            'entity_id' => '14436',
            'in_draft' => '0',
            'uid' => $uid,
            'langcode' => 'en',
            // 'token' => $wedformID[6],
            'uri' => '/unfinished-forms',
            'remote_addr' => '137.54.152.242',
            'data' => [
                'bicycle_licenses_or_permits'=> '987',
                'check_the_approximate_percentage_of_personal_property_tax_receip'=> '50 to 59.9%',
                'construction'=> '',
                'construction_total'=> '',
                'drainage'=> '',
                'drainage_total'=> '',
                'email'=> 'sck7x@virginia.edu',
                'emergency_snow_and_ice_removal'=> '',
                'emergency_snow_and_ice_removal_total'=> '',
                'engineering'=> '',
                'engineering_total'=> '',
                'engineering_where_separable_'=> '',
                'engineering_where_separable_total'=> '',
                'fax'=> '',
                'federal_aviation_administration_faa_'=> '',
                'federal_emergency_management_agency_fema_'=> '',
                'federal_highway_administration_fhwa_'=> '1087',
                'first_name'=> 'Stephen'
                'forest_service'=> '',
                'general_administration_and_miscellaneous_expenditures'=> '',
                'general_administration_and_miscellaneous_expenditures_total'=> '',
                'housing_and_urban_development_hud_'=> '',
                'how_much_did_your_locality_pay_in_interest_and_redemption_paymen'=> '',
                'how_much_did_your_locality_spend_on_law_enforcement_in_2017_'=> '23456',
                'if_you_cannot_provide_an_amount_would_you_have_an_estimated_perc'=> '',
                'impact_fees'=> '',
                'interest_income'=> '4321',
                'last_name'=> 'Kulp',
                'mandated'=> "Mandated",
                'motor_vehicle_license_taxes_sometimes_called_the_decal_tax_'=> '21345',
                'm_i_'=> "G",
                'of_that_amount_what_is_the_estimated_percentage_associated_with_'=> '30 to 39.9%',
                'of_the_total_outstanding_what_amount_was_associated_with_financi'=> '10000000',
                'other'=> '',
                'other_emergency_services'=> '',
                'other_emergency_services_total'=> '',
                'other_total'=> '',
                'other_traffic_services_roadside_'=> '',
                'other_traffic_services_roadside_total'=> '',
                'pavement'=> '100000',
                'pavement_total'=> '100000',
                'personal_property_tax_total_receipts'=> '4321543',
                'phone'=> 434-982-5638,
                'position'=> 'Research Technician',
                'private_contributions'=> '43543',
                'recovered_costs'=> '50000',
                'rights_of_way_eligible'=> '',
                'rights_of_way_total'=> '',
                'road_street_or_highway_related_bonds_redeemed_'=> '1500000',
                'road_street_or_highway_related_bonds_refunded_'=> '200000',
                'special_road_street_and_highway_assessments_imposed_by_your_loca'=> '23456',
                'structures_bridges'=> '',
                'structures_bridges_eligible'=> '',
                'suffix'=> '',
                'taxi_permits'=> '3245',
                'title'=> 'Mr.',
                'traffic_control_operations'=> '',
                'traffic_control_operations_total'=> '50000000',
                'traffic_fines_exclude_parking_fines_'=> '54321',
                'what_amount_of_funds_were_transferred_by_your_locality_to_either'=> '',
                'what_is_the_total_value_of_bonds_outstanding_in_your_locality_as'=> '100000000'
            ],
        ];
        
        // Check webform is open.
        $webform = Webform::load($values['webform_id']);
        //kint($webform);
        if($webform != null){
            //check the webform is open to submit
            $is_open = WebformSubmissionForm::isOpen($webform);
            
            if ($is_open === TRUE) {
              // Validate submission.
                $errors = WebformSubmissionForm::validateValues($values);
                
                // Check there are no validation errors.
                if (!empty($errors)) {
                    //kint($values);
                    //kint($errors);
                    $responseWF = false;
                }
                else {
                    // Submit values and get submission ID.
                    $webform_submission = WebformSubmissionForm::submitValues($values);
                    //kint($webform_submission);
                    if(is_numeric($webform_submission->id()) &&  $webform_submission->id() > 0){
                        $responseWF = true;
                        $response["response"] = "success";
                    }
                }
            }
        }
        
        if($responseWF === false){  
            $response["response"] = "Submission failed. Please contact the site administrator.";
        }
        unset($wedformID);
        return $response;
    }
}












