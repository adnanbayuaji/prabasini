<?php

/**
 * Utility Functions untuk PAP Management System
 */

class Helper {
    
    /**
     * Format datetime untuk display
     */
    public static function formatDateTime($dateString) {
        if (!$dateString) return '-';
        
        try {
            $date = new DateTime($dateString);
            return $date->format('d/m/Y H:i');
        } catch (Exception $e) {
            return '-';
        }
    }
    
    /**
     * Format nomor WhatsApp (add +)
     */
    public static function formatWhatsappNumber($number) {
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // If starts with 62, it's already international format
        if (substr($number, 0, 2) === '62') {
            return $number;
        }
        
        // If starts with 0 (Indonesia), replace with 62
        if ($number[0] === '0') {
            $number = '62' . substr($number, 1);
        }
        
        return $number;
    }
    
    /**
     * Validate nomor WhatsApp
     */
    public static function isValidWhatsappNumber($number) {
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Must be 10-15 digits
        return strlen($number) >= 10 && strlen($number) <= 15;
    }
    
    /**
     * Generate WhatsApp URL
     */
    public static function generateWhatsappUrl($number) {
        $formattedNumber = self::formatWhatsappNumber($number);
        return 'https://wa.me/' . $formattedNumber;
    }
    
    /**
     * Sanitize input string
     */
    public static function sanitizeInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
    }
    
    /**
     * Escape HTML untuk output
     */
    public static function escapeHtml($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Check if string is empty/null
     */
    public static function isEmpty($value) {
        return empty($value) || trim($value) === '';
    }
    
    /**
     * Get file size in human readable format
     */
    public static function getFileSizeHuman($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Generate random string
     */
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    /**
     * Log activity
     */
    public static function logActivity($action, $data = []) {
        $logFile = '../logs/activity.log';
        
        // Create logs directory if not exists
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $message = "[{$timestamp}] {$action}";
        
        if (!empty($data)) {
            $message .= " | " . json_encode($data);
        }
        
        error_log($message . "\n", 3, $logFile);
    }
    
    /**
     * Get status badge HTML
     */
    public static function getStatusBadge($status) {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'sent' => '<span class="badge bg-success">Terkirim</span>',
            'failed' => '<span class="badge bg-danger">Gagal</span>'
        ];
        
        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
    
    /**
     * Check if file is safe to upload
     */
    public static function isFileSafe($filePath) {
        // Check if file exists
        if (!file_exists($filePath)) {
            return false;
        }
        
        // Check file size (max 5MB)
        if (filesize($filePath) > 5 * 1024 * 1024) {
            return false;
        }
        
        // Check file extension
        $allowedExt = ['xls', 'xlsx', 'csv'];
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Delete file safely
     */
    public static function deleteFile($filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            return @unlink($filePath);
        }
        return false;
    }
    
    /**
     * Get current timestamp
     */
    public static function getCurrentTimestamp() {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Get time difference in human readable format
     */
    public static function getTimeDifference($from, $to = null) {
        if ($to === null) {
            $to = date('Y-m-d H:i:s');
        }
        
        $fromTime = strtotime($from);
        $toTime = strtotime($to);
        $diff = $toTime - $fromTime;
        
        if ($diff < 60) {
            return $diff . 's ago';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . 'm ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . 'h ago';
        } else {
            return floor($diff / 86400) . 'd ago';
        }
    }
    
    /**
     * JSON Response Helper
     */
    public static function jsonResponse($success, $message, $data = null, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }
}

?>
