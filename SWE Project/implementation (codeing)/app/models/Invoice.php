<?php

class Invoice {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create new Invoice
     */
    public function create($supplier_id, $po_id, $invoice_number, $invoice_date, $total_amount, $pdf_path = null, $match_status = 'pending') {
        $sql = "INSERT INTO invoice 
                (supplier_id, po_id, invoice_number, invoice_date, total_amount, pdf_path, match_status)
                VALUES 
                (:supplier_id, :po_id, :invoice_number, :invoice_date, :total_amount, :pdf_path, :match_status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':supplier_id'    => $supplier_id,
            ':po_id'          => $po_id,
            ':invoice_number' => $invoice_number,
            ':invoice_date'   => $invoice_date,
            ':total_amount'   => $total_amount,
            ':pdf_path'       => $pdf_path,
            ':match_status'   => $match_status
        ]);
    }

    /**
     * Get Invoice by ID
     */
    public function getById($invoice_id) {
        $sql = "SELECT * FROM invoice WHERE invoice_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $invoice_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get Invoices by Purchase Order
     */
    public function getByPO($po_id) {
        $sql = "SELECT * FROM invoice WHERE po_id = :po_id ORDER BY invoice_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':po_id' => $po_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Invoices by Supplier
     */
    public function getBySupplier($supplier_id) {
        $sql = "SELECT i.*, po.po_number 
                FROM invoice i
                LEFT JOIN purchaseorder po ON i.po_id = po.po_id
                WHERE i.supplier_id = :supplier_id 
                ORDER BY i.invoice_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':supplier_id' => $supplier_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get All Invoices
     */
    public function getAll() {
        $sql = "SELECT i.*, s.name as supplier_name, po.po_number 
                FROM invoice i
                LEFT JOIN supplier s ON i.supplier_id = s.supplier_id
                LEFT JOIN purchaseorder po ON i.po_id = po.po_id
                ORDER BY i.invoice_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update Match Status
     */
    public function updateMatchStatus($invoice_id, $match_status) {
        $sql = "UPDATE invoice SET match_status = :match_status WHERE invoice_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':match_status' => $match_status,
            ':id'           => $invoice_id
        ]);
    }

    /**
     * Update Total Amount (useful if corrections are needed)
     */
    public function updateTotal($invoice_id, $total_amount) {
        $sql = "UPDATE invoice SET total_amount = :total_amount WHERE invoice_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':total_amount' => $total_amount,
            ':id'           => $invoice_id
        ]);
    }

    /**
     * Update PDF Path
     */
    public function updatePdfPath($invoice_id, $pdf_path) {
        $sql = "UPDATE invoice SET pdf_path = :pdf_path WHERE invoice_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pdf_path' => $pdf_path,
            ':id'       => $invoice_id
        ]);
    }

    /**
     * Delete Invoice
     */
    public function delete($invoice_id) {
        $sql = "DELETE FROM invoice WHERE invoice_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $invoice_id]);
    }
}


// the file was empty , i added this but maybe it noy really working
