<?php

namespace Drupal\vdot_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformSubmissionForm;
use Symfony\Component\HttpFoundation\Response;

class CustomModController extends ControllerBase
{
    public function import_webform_csv()
    {
        $filePath = \Drupal::service('file_system')->realpath(file_default_scheme().'://');
        $filename = $filePath.'/current.csv';
        $handle = fopen($filename, 'r');
        //kint($handle);
        $arrSubmission = array();
        $final_response = '<h2>Webform Submissions</h2><table><th>Locality</th><th>Status</th>';
        ini_set('auto_detect_line_endings', true);
        while (false !== ($rowData = fgetcsv($handle))) {
            //kint($rowData);
            $response = $this->add_rsvp_submission($rowData);
            $final_response = $final_response.$response;
            //kint($response);
        }
        fclose($handle);
        $final_response = $final_response.'</table>';

        return Response::create($final_response, 200);
    }

    public function add_rsvp_submission($rowData = array())
    {
        foreach ($rowData as $data) {
            $wedformID[] = $data;
        }
        //add to webform and send edm
        $responseWF = false;
        $values = null;
        //Get the User ID
        $name = $wedformID[1];
        $users = \Drupal::entityTypeManager()->getStorage('user')
          ->loadByProperties(['name' => $name]);
        $user = reset($users);
        if ($user) {
            $uid = $user->id();
            $rids = $user->getRoles();
        } else {
            echo 'No UID for '.$name;
        }

        $other_agencies = array();
        $other_agencies_data = array(
            'agency' => $wedformID[38],
            'amount' => $wedformID[39],
        );
        $other_agencies[] = $other_agencies_data;

        $law_enforcement_percentage = $this->convert_num_to_percent_range_law_enforcement($wedformID[64]); //64 is empty in 2004 - 24 is supposed to be the percentage
        $percent_personal_prop_tax_from_vehicles = $this->convert_num_to_percent_range($wedformID[9]);
        $email = preg_replace('/\s+/', '', $wedformID[3]);

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
                'email' => $email,
                'year' => $wedformID[4],
                'actual_spending_on_eligible_facilities' => $wedformID[5],
                'of_the_total_outstanding_what_amount_was_associated_with_financi' => $wedformID[7],
                'bicycle_licenses_or_permits' => $wedformID[8],
                'check_the_approximate_percentage_of_personal_property_tax_receip' => $percent_personal_prop_tax_from_vehicles,
                'comments' => $wedformID[10],
                'first_name' => $wedformID[11],
                'construction' => $wedformID[12],
                'construction_total' => $wedformID[13],
                'how_much_did_your_locality_pay_in_interest_and_redemption_paymen' => $wedformID[14],
                'drainage' => $wedformID[15],
                'drainage_total' => $wedformID[16],
                'emergency_snow_and_ice_removal' => $wedformID[17],
                'emergency_snow_and_ice_removal_total' => $wedformID[18],
                'engineering_where_separable_' => $wedformID[19],
                'engineering_where_separable_total' => $wedformID[20],
                'engineering' => $wedformID[21],
                'engineering_total' => $wedformID[22],
                'if_you_cannot_provide_an_amount_would_you_have_an_estimated_perc' => $wedformID[23],
                'of_that_amount_what_is_the_estimated_percentage_associated_with_' => $law_enforcement_percentage,
                'fax' => $wedformID[25],
                'federal_aviation_administration_faa_' => $wedformID[26],
                'federal_emergency_management_agency_fema_' => $wedformID[27],
                'federal_highway_administration_fhwa_' => $wedformID[28],
                'forest_service' => $wedformID[29],
                'general_administration_and_miscellaneous_expenditures' => $wedformID[30],
                'general_administration_and_miscellaneous_expenditures_total' => $wedformID[31],
                'housing_and_urban_development_hud_' => $wedformID[32],
                'impact_fees' => $wedformID[33],
                'interest_income' => $wedformID[34],
                'last_name' => '',
                'mandated' => 'Unknown',
                'motor_vehicle_license_taxes_sometimes_called_the_decal_tax_' => $wedformID[35],
                'm_i_' => '',
                'other' => $wedformID[36],
                'other_total' => $wedformID[37],
                'other_agencies_please_specify_' => $other_agencies,
                'other_emergency_services' => $wedformID[40],
                'other_emergency_services_total' => $wedformID[41],
                'other_traffic_services_roadside_' => $wedformID[42],
                'other_traffic_services_roadside_total' => $wedformID[43],
                'what_is_the_total_value_of_bonds_outstanding_in_your_locality_as' => $wedformID[44],
                'pavement' => $wedformID[45],
                'pavement_total' => $wedformID[46],
                'what_amount_of_funds_were_transferred_by_your_locality_to_either' => $wedformID[47],
                'personal_property_tax_total_receipts' => $wedformID[48],
                'phone' => $wedformID[49],
                'position' => $wedformID[50],
                'private_contributions' => $wedformID[51],
                // 52 - 54 are receipt values
                'recovered_costs' => $wedformID[55],
                'rights_of_way_eligible' => $wedformID[56],
                'rights_of_way_total' => $wedformID[57],
                'road_street_or_highway_related_bonds_redeemed_' => $wedformID[58],
                'road_street_or_highway_related_bonds_refunded_' => $wedformID[59],
                'special_road_street_and_highway_assessments_imposed_by_your_loca' => $wedformID[60],
                'structures_bridges_eligible' => $wedformID[61],
                'structures_bridges' => $wedformID[62],
                'suffix' => '',
                'taxi_permits' => $wedformID[63],
                // 'how_much_did_your_locality_spend_on_law_enforcement_in_2017_' => $wedformID[64], //64 is blank - total is actually in 24
                'how_much_did_your_locality_spend_on_law_enforcement_in_2017_' => $wedformID[24],
                'traffic_control_devices_eligible' => $wedformID[65],
                'traffic_control_devices' => $wedformID[66],
                'title' => 'Mr.',
                'traffic_control_operations' => $wedformID[67],
                'traffic_control_operations_total' => $wedformID[68],
                'traffic_fines_exclude_parking_fines_' => $wedformID[69],
                'transfers_from_toll_facilities' => $wedformID[70],
            ],
        ];

        // Check webform is open.
        $webform = Webform::load($values['webform_id']);
        //kint($webform);
        if (null != $webform) {
            //check the webform is open to submit
            $is_open = WebformSubmissionForm::isOpen($webform);

            if (true === $is_open) {
                // Validate submission.
                $errors = WebformSubmissionForm::validateFormValues($values);

                // Check there are no validation errors.
                if (!empty($errors)) {
                    //kint($values);
                    //kint($errors);
                    $responseWF = false;
                } else {
                    // Submit values and get submission ID.
                    $webform_submission = WebformSubmissionForm::submitFormValues($values);
                    //kint($webform_submission);
                    if (is_numeric($webform_submission->id()) && $webform_submission->id() > 0) {
                        $responseWF = true;
                        $response = '<tr><td>'.$wedformID[1].'</td><td>success</td></tr>';
                    }
                }
            }
        }

        if (false === $responseWF) {
            $response = '<tr style="color:red;"><td>'.$wedformID[1].'</td><td>FAILED</td></tr>';
        }
        unset($wedformID);

        return $response;
    }

    public function convert_num_to_percent_range($index)
    {
        if (1 == $index) {
            return 'Less than 50%';
        } elseif (2 == $index) {
            return '50 to 59.9%';
        } elseif (3 == $index) {
            return '60 to 69.9%';
        } elseif (4 == $index) {
            return '70 to 79.9%';
        } elseif (5 == $index) {
            return '80 to 89.9%';
        } elseif (6 == $index) {
            return '90 to 100%';
        } else {
            return '0%';
        }
    }

    public function convert_num_to_percent_range_law_enforcement($index)
    {
        if (1 == $index) {
            return 'Less than 20%';
        } elseif (2 == $index) {
            return '20 to 29.9%';
        } elseif (3 == $index) {
            return '30 to 39.9%';
        } elseif (4 == $index) {
            return '40 to 49.9%';
        } elseif (5 == $index) {
            return '50 to 59.9%';
        } elseif (6 == $index) {
            return '60 to 69.9%';
        } elseif (7 == $index) {
            return '70 to 79.9%';
        } elseif (8 == $index) {
            return '80 to 89.9%';
        } elseif (9 == $index) {
            return '90 to 100%';
        } else {
            return 'unknown';
        }
    }
}
