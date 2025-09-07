 <?php
     require 'conn.php';
    
    $data = array();
    
    $query = mysqli_query($conn, "SELECT * FROM course_category ORDER BY category_name ASC");
    if(mysqli_num_rows($query) > 0){ 
        while ($row = mysqli_fetch_assoc($query)) {
            $category = $row['category_name'];
             $data[] = array(
                      'category' => $category
                  );
        }
    }else{ $data = array('Info' => 'No category found'); }
    
    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);

?>