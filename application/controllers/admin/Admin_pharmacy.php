<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_pharmacy extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin', 'receptionist'));
        $this->load->model('Pharmacy_bill_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Pharmacy Billing',
            'active_menu' => 'pharmacy_billing',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/pharmacy/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function create($prescription_id = NULL)
    {
        $data = array(
            'page_title' => 'Generate Pharmacy Bill',
            'active_menu' => 'pharmacy_billing',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'prescriptions' => $this->Pharmacy_bill_model->get_unbilled_prescriptions(),
            'selected_prescription_id' => $prescription_id
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/pharmacy/create', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    // FIXED: Method name matches route
    public function get_bills_ajax()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', TRUE)),
            'bill_date' => trim((string) $this->input->get('bill_date', TRUE))
        );

        $bills = $this->Pharmacy_bill_model->get_all($filters);

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $bills
        ));
    }

    // FIXED: Method name matches route
    public function get_prescription_ajax($id)
    {
        // Debug logging
        log_message('debug', 'Getting prescription details for ID: ' . $id);
        
        $prescription = $this->Pharmacy_bill_model->get_prescription_details((int) $id);

        if (empty($prescription)) {
            log_message('debug', 'Prescription not found or already billed for ID: ' . $id);
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Prescription not found or already billed.'
            ));
        }

        $items = $this->Pharmacy_bill_model->get_prescription_items_for_billing((int) $id);
        
        log_message('debug', 'Found ' . count($items) . ' items for prescription ID: ' . $id);

        return $this->json_response(array(
            'status' => TRUE,
            'prescription' => $prescription,
            'items' => $items
        ));
    }

    public function store()
    {
        // Debug: Log the request
        log_message('debug', '=== PHARMACY BILL STORE ===');
        log_message('debug', 'Request method: ' . $this->input->method());
        log_message('debug', 'POST data: ' . print_r($this->input->post(NULL, TRUE), true));

        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        // Validate the form
        $this->form_validation->set_rules('prescription_id', 'Prescription', 'trim|required|integer');
        $this->form_validation->set_rules('bill_date', 'Bill Date', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => strip_tags(validation_errors())
            ));
        }

        $prescription_id = (int) $this->input->post('prescription_id', TRUE);
        $bill_date = trim((string) $this->input->post('bill_date', TRUE));

        // Check if prescription already has a bill
        if ($this->Pharmacy_bill_model->prescription_has_bill($prescription_id)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'This prescription has already been billed.'
            ));
        }

        // Get prescription details
        $prescription = $this->Pharmacy_bill_model->get_prescription_details($prescription_id);

        if (empty($prescription)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please select a valid unbilled prescription.'
            ));
        }

        // Get items from POST
        $posted_items = $this->input->post('items', TRUE);
        
        log_message('debug', 'Posted items: ' . print_r($posted_items, true));

        if (empty($posted_items) || !is_array($posted_items)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'No medicines found for billing.'
            ));
        }

        // Process items
        $prescription_items = $this->Pharmacy_bill_model->get_prescription_items_for_billing($prescription_id);
        $prescription_item_map = array();
        $validated_items = array();
        $total_amount = 0.00;
        $medicine_stock_usage = array();

        foreach ($prescription_items as $item) {
            $prescription_item_map[(int) $item['prescription_item_id']] = $item;
        }

        foreach ($posted_items as $posted_item) {
            $prescription_item_id = isset($posted_item['prescription_item_id']) ? (int) $posted_item['prescription_item_id'] : 0;
            $quantity = isset($posted_item['quantity']) ? (int) $posted_item['quantity'] : 0;

            if ($prescription_item_id <= 0 || !isset($prescription_item_map[$prescription_item_id])) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Invalid medicine row detected.'
                ));
            }

            if ($quantity <= 0) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Medicine quantity must be greater than zero.'
                ));
            }

            $source_item = $prescription_item_map[$prescription_item_id];
            $medicine_id = (int) $source_item['medicine_id'];

            if (!isset($medicine_stock_usage[$medicine_id])) {
                $medicine_stock_usage[$medicine_id] = 0;
            }

            $medicine_stock_usage[$medicine_id] += $quantity;

            if ($medicine_stock_usage[$medicine_id] > (int) $source_item['stock_quantity']) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Requested quantity exceeds available stock for ' . $source_item['medicine_name'] . '. Available: ' . $source_item['stock_quantity']
                ));
            }

            $unit_price = (float) $source_item['unit_price'];
            $line_total = $quantity * $unit_price;

            $validated_items[] = array(
                'prescription_item_id' => $prescription_item_id,
                'medicine_id' => $medicine_id,
                'quantity' => $quantity,
                'unit_price' => number_format($unit_price, 2, '.', ''),
                'line_total' => number_format($line_total, 2, '.', '')
            );

            $total_amount += $line_total;
        }

        if ($total_amount <= 0) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Bill total must be greater than zero.'
            ));
        }

        // Generate bill code
        $bill_code = $this->Pharmacy_bill_model->generate_bill_code();

        if ($this->Pharmacy_bill_model->bill_code_exists($bill_code)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to generate a unique bill code. Please try again.'
            ));
        }

        // Prepare bill data
        $bill_data = array(
            'bill_code' => $bill_code,
            'prescription_id' => (int) $prescription['id'],
            'patient_id' => (int) $prescription['patient_id'],
            'doctor_id' => (int) $prescription['doctor_id'],
            'bill_date' => $bill_date,
            'total_amount' => number_format((float) $total_amount, 2, '.', ''),
            'generated_by' => $this->current_user_id()
        );

        log_message('debug', 'Bill data: ' . print_r($bill_data, true));
        log_message('debug', 'Items: ' . print_r($validated_items, true));

        // Create the bill
        $created = $this->Pharmacy_bill_model->create_bill($bill_data, $validated_items);

        if (!$created) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to save bill. Please verify stock availability and try again.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Pharmacy bill saved successfully and medicine stock has been deducted.',
            'redirectUrl' => site_url('admin/pharmacy-billing')
        ));
    }

    // protected function json_response($response)
    // {
    //     $response['csrfHash'] = $this->security->get_csrf_hash();

    //     return $this->output
    //         ->set_content_type('application/json')
    //         ->set_output(json_encode($response));
    // }
}