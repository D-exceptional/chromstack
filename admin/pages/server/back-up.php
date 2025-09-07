<?php

require "conn.php";

// Check if the connection was successful
if (!$conn) {
    echo json_encode(['Info' => 'Database connection error: ' . mysqli_connect_error()]);
    exit();
}

$data = [];

// Backup transaction_payments table
$query = mysqli_query($conn, "SELECT * FROM transaction_payments");

if ($query) {
    if (mysqli_query($conn, "DELETE FROM transaction_payments")) {
        // Truncate tables
        $tablesToClear = [
            'affiliate_course_sales',
            'uploaded_course_sales',
            'membership_payment'
        ];
        
        foreach ($tablesToClear as $table) {
            if (!mysqli_query($conn, "DELETE FROM $table")) {
                $data['Info'] = 'Error clearing table ' . $table;
                echo json_encode($data);
                mysqli_close($conn);
                exit();
            }
        }

        // Update and backup withdrawals
        $sql = mysqli_query($conn, "SELECT * FROM withdrawals");

        if ($sql) {
            if (mysqli_num_rows($sql) > 0) {
                if (mysqli_query($conn, "UPDATE withdrawals SET withdrawal_status = 'Completed'")) {
                    if (mysqli_query($conn, "INSERT INTO withdrawal_history SELECT * FROM withdrawals")) {
                        if (mysqli_query($conn, "DELETE FROM withdrawals")) {
                            $data = ['Info' => 'Backup operations were successful'];
                        } else {
                            $data = ['Info' => 'Error truncating withdrawals table'];
                        }
                    } else {
                        $data = ['Info' => 'Error inserting into withdrawal_history'];
                    }
                } else {
                    $data = ['Info' => 'Error updating withdrawals'];
                }
            } else {
                $data = ['Info' => 'No withdrawals to process'];
            }
        } else {
            $data = ['Info' => 'Error selecting from withdrawals'];
        }
    } else {
        $data = ['Info' => 'Error backing up transaction_payments'];
    }
} else {
    $data = ['Info' => 'No records found in transaction_payments'];
}

// Output the result as JSON
echo json_encode($data);

mysqli_close($conn);
exit();

?>
