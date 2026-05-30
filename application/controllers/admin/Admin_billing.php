<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_billing extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin', 'receptionist'));
        $this->load->model('Billing_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Billing',
            'active_menu' => 'billing',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load_admin_view('admin/billing/index', $data);
    }

    public function create($appointment_id = NULL)
    {
        $data = array(
            'page_title' => 'Generate Invoice',
            'active_menu' => 'billing',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'appointments' => $this->Billing_model->get_unbilled_appointments(),
            'selected_appointment_id' => $appointment_id
        );

        $this->load_admin_view('admin/billing/create', $data);
    }

    public function get_bills_ajax()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', TRUE)),
            'invoice_date' => trim((string) $this->input->get('invoice_date', TRUE)),
            'payment_status' => trim((string) $this->input->get('payment_status', TRUE))
        );

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->Billing_model->get_all($filters)
        ));
    }

    public function preview($appointment_id)
    {
        $preview = $this->Billing_model->get_invoice_preview((int) $appointment_id);

        if (!$preview) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Appointment not found, cancelled, or already billed.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $preview
        ));
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $validated = $this->validate_invoice_form();

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $validated['message']
            ));
        }

        $preview = $validated['preview'];
        $invoice_no = $this->Billing_model->generate_invoice_no();

        if ($this->Billing_model->invoice_no_exists($invoice_no)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to generate a unique invoice number. Please try again.'
            ));
        }

        $payment_status = trim((string) $this->input->post('payment_status', TRUE));
        $payment_method = trim((string) $this->input->post('payment_method', TRUE));

        if ($payment_status === 'unpaid') {
            $payment_method = NULL;
        }

        $billing_data = array(
            'invoice_no' => $invoice_no,
            'appointment_id' => (int) $preview['appointment']['id'],
            'patient_id' => (int) $preview['appointment']['patient_id'],
            'doctor_id' => (int) $preview['appointment']['doctor_id'],
            'invoice_date' => trim((string) $this->input->post('invoice_date', TRUE)),
            'consultation_fee' => $preview['consultation_fee'],
            'pharmacy_total' => $preview['pharmacy_total'],
            'lab_total' => $preview['lab_total'],
            'total_amount' => $preview['total_amount'],
            'payment_status' => $payment_status,
            'payment_method' => $payment_method,
            'paid_at' => ($payment_status === 'paid') ? date('Y-m-d H:i:s') : NULL,
            'generated_by' => $this->current_user_id()
        );

        $created = $this->Billing_model->create_invoice($billing_data, $preview['items']);

        if (!$created) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to save invoice at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Invoice generated successfully.',
            'redirectUrl' => site_url('admin/billing/view/' . (int) $created)
        ));
    }

    public function view($id)
    {
        $invoice = $this->Billing_model->get_invoice((int) $id);

        if (empty($invoice)) {
            show_404();
            return;
        }

        $data = array(
            'page_title' => 'Invoice ' . $invoice['invoice_no'],
            'active_menu' => 'billing',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'invoice' => $invoice,
            'items' => $this->Billing_model->get_invoice_items((int) $id)
        );

        $this->load_admin_view('admin/billing/view', $data);
    }

    public function pdf($id)
    {
        $invoice = $this->Billing_model->get_invoice((int) $id);

        if (empty($invoice)) {
            show_404();
            return;
        }

        $data = array(
            'invoice' => $invoice,
            'items' => $this->Billing_model->get_invoice_items((int) $id)
        );

        $html = $this->load->view('admin/billing/pdf_invoice', $data, TRUE);

        $this->load->library('Pdf');

        $pdf = new Pdf('P', 'mm', 'A4', TRUE, 'UTF-8', FALSE);
        $pdf->SetCreator('HMS');
        $pdf->SetAuthor('Hospital Management System');
        $pdf->SetTitle('Invoice ' . $invoice['invoice_no']);
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetAutoPageBreak(TRUE, 16);
        $pdf->AddPage();
        $pdf->writeHTML($html, TRUE, FALSE, TRUE, FALSE, '');
        $pdf->Output($invoice['invoice_no'] . '.pdf', 'I');
    }

    protected function validate_invoice_form()
    {
        $this->form_validation->set_rules('appointment_id', 'Appointment', 'trim|required|integer');
        $this->form_validation->set_rules('invoice_date', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('payment_status', 'Payment Status', 'trim|required|in_list[unpaid,partial,paid]');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|in_list[cash,card,upi,other]');

        if ($this->form_validation->run() === FALSE) {
            return array(
                'status' => FALSE,
                'message' => strip_tags(validation_errors())
            );
        }

        $appointment_id = (int) $this->input->post('appointment_id', TRUE);
        $payment_status = trim((string) $this->input->post('payment_status', TRUE));
        $payment_method = trim((string) $this->input->post('payment_method', TRUE));

        if ($this->Billing_model->invoice_exists_for_appointment($appointment_id)) {
            return array(
                'status' => FALSE,
                'message' => 'An invoice already exists for this appointment.'
            );
        }

        if (($payment_status === 'paid' || $payment_status === 'partial') && $payment_method === '') {
            return array(
                'status' => FALSE,
                'message' => 'Payment method is required for paid or partial invoices.'
            );
        }

        $preview = $this->Billing_model->get_invoice_preview($appointment_id);

        if (!$preview) {
            return array(
                'status' => FALSE,
                'message' => 'Appointment not found, cancelled, or already billed.'
            );
        }

        if ((float) $preview['total_amount'] <= 0) {
            return array(
                'status' => FALSE,
                'message' => 'Invoice total must be greater than zero.'
            );
        }

        return array(
            'status' => TRUE,
            'preview' => $preview
        );
    }

    protected function load_admin_view($view, $data)
    {
        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view($view, $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    protected function json_response($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();
        
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
