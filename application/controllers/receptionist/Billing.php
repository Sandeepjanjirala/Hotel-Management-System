<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing extends Receptionist_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receptionist_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->render('receptionist/billing/index', array(
            'page_title' => 'Billing',
            'active_menu' => 'billing'
        ));
    }

    public function ajax_list()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', true)),
            'invoice_date' => trim((string) $this->input->get('invoice_date', true)),
            'payment_status' => trim((string) $this->input->get('payment_status', true))
        );

        return $this->json_response(array(
            'status' => true,
            'data' => $this->Receptionist_model->get_bills($filters, 75, 0)
        ));
    }

    public function create()
    {
        $this->render('receptionist/billing/create', array(
            'page_title' => 'Generate Invoice',
            'active_menu' => 'billing',
            'appointments' => $this->Receptionist_model->get_unbilled_appointments()
        ));
    }

    public function preview($appointment_id)
    {
        $preview = $this->Receptionist_model->get_invoice_preview((int) $appointment_id);

        return $this->json_response(array(
            'status' => (bool) $preview,
            'data' => $preview,
            'message' => $preview ? '' : 'Appointment not found, cancelled, or already billed.'
        ));
    }

    public function store()
    {
        $this->form_validation->set_rules('appointment_id', 'Appointment', 'trim|required|integer');
        $this->form_validation->set_rules('invoice_date', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('payment_status', 'Payment Status', 'trim|required|in_list[unpaid,partial,paid]');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|in_list[cash,card,upi,other]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect('receptionist/billing/create');
            return;
        }

        $billing_id = $this->Receptionist_model->create_invoice(
            (int) $this->input->post('appointment_id', true),
            trim((string) $this->input->post('invoice_date', true)),
            trim((string) $this->input->post('payment_status', true)),
            trim((string) $this->input->post('payment_method', true)),
            $this->receptionist_user_id
        );

        $this->session->set_flashdata($billing_id ? 'success' : 'error', $billing_id ? 'Invoice generated successfully.' : 'Unable to generate invoice.');
        redirect($billing_id ? 'receptionist/billing/view/' . (int) $billing_id : 'receptionist/billing/create');
    }

    public function view($id)
    {
        $invoice = $this->Receptionist_model->get_invoice((int) $id);
        if (!$invoice) {
            show_404();
            return;
        }

        $this->render('receptionist/billing/view', array(
            'page_title' => 'Invoice ' . $invoice['invoice_no'],
            'active_menu' => 'billing',
            'invoice' => $invoice,
            'items' => $this->Receptionist_model->get_invoice_items((int) $id)
        ));
    }

    public function mark_payment($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('receptionist/billing');
            return;
        }

        $status = trim((string) $this->input->post('payment_status', true));
        $method = trim((string) $this->input->post('payment_method', true));
        $updated = in_array($status, array('unpaid', 'partial', 'paid'), true)
            ? $this->Receptionist_model->mark_payment_status((int) $id, $status, $method)
            : false;

        $this->session->set_flashdata($updated ? 'success' : 'error', $updated ? 'Payment status updated.' : 'Unable to update payment status.');
        redirect('receptionist/billing/view/' . (int) $id);
    }
}
