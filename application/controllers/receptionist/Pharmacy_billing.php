<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_billing extends Receptionist_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receptionist_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->render('receptionist/pharmacy/index', array(
            'page_title' => 'Pharmacy Billing',
            'active_menu' => 'pharmacy'
        ));
    }

    public function ajax_list()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', true)),
            'bill_date' => trim((string) $this->input->get('bill_date', true))
        );

        return $this->json_response(array(
            'status' => true,
            'data' => $this->Receptionist_model->get_pharmacy_bills($filters, 75, 0)
        ));
    }

    public function create()
    {
        $this->render('receptionist/pharmacy/create', array(
            'page_title' => 'Generate Pharmacy Bill',
            'active_menu' => 'pharmacy',
            'prescriptions' => $this->Receptionist_model->get_unbilled_prescriptions()
        ));
    }

    public function prescription($id)
    {
        $prescription = $this->Receptionist_model->get_prescription_details((int) $id);

        return $this->json_response(array(
            'status' => (bool) $prescription,
            'prescription' => $prescription,
            'items' => $prescription ? $this->Receptionist_model->get_prescription_items((int) $id) : array(),
            'message' => $prescription ? '' : 'Prescription not found or already billed.'
        ));
    }

    public function store()
    {
        $this->form_validation->set_rules('prescription_id', 'Prescription', 'trim|required|integer');
        $this->form_validation->set_rules('bill_date', 'Bill Date', 'trim|required');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect('receptionist/pharmacy-billing/create');
            return;
        }

        $bill_id = $this->Receptionist_model->create_pharmacy_bill(
            (int) $this->input->post('prescription_id', true),
            trim((string) $this->input->post('bill_date', true)),
            $this->input->post('items') ?: array(),
            $this->receptionist_user_id
        );

        $this->session->set_flashdata($bill_id ? 'success' : 'error', $bill_id ? 'Pharmacy bill generated successfully.' : 'Unable to generate pharmacy bill.');
        redirect($bill_id ? 'receptionist/pharmacy-billing/view/' . (int) $bill_id : 'receptionist/pharmacy-billing/create');
    }

    public function view($id)
    {
        $bill = $this->Receptionist_model->get_pharmacy_bill((int) $id);
        if (!$bill) {
            show_404();
            return;
        }

        $this->render('receptionist/pharmacy/view', array(
            'page_title' => 'Pharmacy Bill ' . $bill['bill_code'],
            'active_menu' => 'pharmacy',
            'bill' => $bill,
            'items' => $this->Receptionist_model->get_pharmacy_bill_items((int) $id)
        ));
    }
}
