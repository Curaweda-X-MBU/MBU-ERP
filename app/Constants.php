<?php

namespace App;

class Constants
{
    public const SUPPLIER_TYPE = [
        1 => 'Bisnis',
        2 => 'Individual',
    ];

    public const CUSTOMER_TYPE = [
        1 => 'Bisnis',
        2 => 'Individual',
    ];

    public const AUDIT_DOC_PRIORITY = [
        1 => 'Sangat Penting',
        2 => 'Penting',
        3 => 'Biasa',
    ];

    public const AUDIT_DOC_PRIORITY_COLOR = [
        1 => 'danger',
        2 => 'warning',
        3 => 'success',
    ];

    public const AUDIT_DOC_CATEGORY = [
        1 => 'Produksi',
        2 => 'Penjualan',
        3 => 'Keuangan',
        4 => 'SDM',
        5 => 'Lain-lain',
    ];

    public const AUDIT_DOC_PATH = 'audit-document';

    public const CHICKIN_DOC_PATH = 'project-chickin';

    public const KANDANG_TYPE = [
        1 => 'Own Farm',
        2 => 'Kemitraan',
    ];

    public const PH_SIGNATURE = [
        'Diketahui Oleh' => [
            'Head Of Poultry Broiler Commercial' => 'Slamet Muryo Kristiono',
            'Poultry Health Manager'             => 'Drh. Maulana Sydik',
        ],
        'Disetujui Oleh' => ['Upstream Vice Director' => 'Fransiscus'],
    ];

    public const PH_FARMING_TYPE = [
        1 => 'Broiler',
        2 => 'Parent Stock',
        3 => 'Layer',
    ];

    public const PH_IMAGE_PATH = 'ph-evidence';

    public const WAREHOUSE_TYPE = [
        1 => 'Lokasi',
        2 => 'Kandang',
    ];

    public const PROJECT_CHICKIN_STATUS = [
        1 => 'Belum',
        2 => 'Pengajuan',
        3 => 'Sudah',
    ];

    public const PROJECT_STATUS = [
        1 => 'Pengajuan',
        2 => 'Aktif',
        3 => 'Persiapan',
        4 => 'Selesai',
    ];

    public const RECORDING_INTERVAL = [
        'Harian', 'Mingguan',
    ];

    public const PURCHASE_PAYMENT_STATUS = [
        0 => 'Menunggu Persetujuan',
        1 => 'Disetujui',
    ];

    public const PURCHASE_STATUS = [
        0 => 'Pengajuan',
        1 => 'Approval Manager',
        2 => 'Approval Poultry Health',
        3 => 'Approval Purchasing',
        4 => 'Approval Finance',
        5 => 'Approval Dir. Finance',
        6 => 'Produk Diterima',
        7 => 'Dibayar Sebagian',
        8 => 'Lunas',
    ];

    public const PURCHASE_APPROVAL = [
        'Approval Manager'        => 'Manager Area',
        'Approval Poultry Health' => 'Manager Poultry Health',
        'Approval Purchasing'     => 'Manager Purchasing',
        'Approval Finance'        => 'Manager Finance',
        'Approval Dir. Finance'   => 'Direktur Finance',
        'Produk Diterima'         => 'Manager Purchasing',
        'Dibayar Sebagian'        => 'Staff Finance',
        'Lunas'                   => 'Staff Finance',
    ];

    public const PURCHASE_RECEPTION_DOC = 'purchase-travel-letter';

    public const PAYMENT_METHOD = [
        1 => 'Transfer',
        2 => 'Cash',
        3 => 'Card',
        4 => 'Cheque',
    ];

    public const PAYMENT_STATUS = [
        0 => 'Menunggu persetujuan',
        1 => 'Disetujui',
        2 => 'Ditolak',
    ];

    public const PAYMENT_DOC = 'payment-document';

    public const INVENTORY_BY = [
        'Penyesuaian',
        'Pembelian',
        'Penjualan',
        'Deplesi',
        'Recording',
    ];

    public const MARKETING_PAYMENT_STATUS = [
        1 => 'Tempo',
        2 => 'Dibayar Sebagian',
        3 => 'Dibayar Penuh',
    ];

    public const MARKETING_STATUS = [
        1 => 'Diajukan',
        2 => 'Penawaran',
        3 => 'Final',
        4 => 'Realisasi',
    ];

    public const MARKETING_DOC_REFERENCE_PATH = 'marketing-doc-reference';

    public const MARKETING_PAYMENT_DOC_PATH = 'marketing-payment-doc';

    public const MARKETING_APPROVAL = [
        0 => 'Tidak Disetujui',
        1 => 'Disetujui',
    ];

    public const RECORDING_STATUS = [
        1 => 'Pengajuan',
        2 => 'Disetujui',
    ];

    public const RECORDING_DOC = 'DOC';

    public const STOCK_EGG_CATEGORIES = [
        1 => 'good',
        2 => 'big',
        3 => 'small',
        4 => 'crack',
        5 => 'dirty',
        6 => 'white',
        7 => 'broken',
        8 => 'broken_good',
        9 => 'sold',
    ];

    public const STOCK_CHICKEN_CATEGORIES = [
        1 => 'good',
        2 => 'death',
        3 => 'culling',
        4 => 'afkir',
        5 => 'sold',
    ];

    public const STOCK_GENERAL_CATEGORIES = [
        1 => 'good',
        2 => 'damaged',
        3 => 'expired',
    ];

    public const CATEGORY_PRODUCT_RECORDING = ['DOC', 'TLR'];

    public const REVISION_STATUS = [
        0 => 'Tidak ada perubahan',
        1 => 'Menunggu persetujuan',
        2 => 'Disetujui',
        3 => 'Selesai',
        4 => 'Ditolak',
    ];

    public const REVISION_DOC_PATH = 'recording-revision-doc';

    public const INVENTORY_MOVEMENT = 'inventory-movement-doc';
}
