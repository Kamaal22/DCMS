<!DOCTYPE html>
<html>

<head>
    <title>Staff Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once('header.php');
    include_once('conn.php') ?>
</head>

<body>

    <div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh;">

        <div class="row mt-5 p-3 shadow-lg rounded">
        <div class='small' id='small'></div>
            <div class='d-flex justify-content-around mb-4'>
                <h2 class="text-center text-primary">Staff List</h2>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staffModal">
                    ADD NEW STUFF
                </button>
            </div>

            <table class="table table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Select all staff from the database
                    $result = mysqli_query($conn, "SELECT * FROM staff");

                    // Loop through the results and output each staff member as a table row
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['staff_id'] . "</td>";
                        echo "<td>" . $row['first_name'] . "</td>";
                        echo "<td>" . $row['last_name'] . "</>";
                        echo "<td>" . $row['phone_number'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['address'] . "</td>";
                        echo "<td class='text-center'> 
                                    <button  class='btn btn-primary' onclick='editStaff(" . $row['staff_id'] . ")'> EDIT </button> 
                                    <a href='#' class='btn btn-danger ms-2 mt-1' onclick='deleteStaff(" . $row['staff_id'] . ")'> DELETE </a> 
                                  </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>



<script>
    function editStaff(ids) {

        var id = ids;
        $('#id').val(id);
        $.ajax({
            url: 'staff/getStaff.php',
            type: 'POST',
            data: {
                updateid: id
            },
            success: function(response) {
                var data = JSON.parse(response);
                $('#first_name').val(data.first_name);
                $('#last_name').val(data.last_name);
                $('#phone_number').val(data.phone_number);
                $('#email').val(data.email);
                $('#address').val(data.address);

                $('#submit').text('update staff')
            }

        });

        $("#submit").text('Update');
        //toggle modal
        $('#staffModal').modal('show');
    }

    function deleteStaff(id) {
        var id = id;
        $.ajax({
            url: 'staff/deleteStaff.php',
            type: 'POST',
            data: {
                deleteid: id
            },
            success: function(response) {
                var obj = jQuery.parseJSON(response);
                if (obj.status == 200) {
                    location.reload();
                } else {
                    alert(obj.message);
                }
            }
        });
    }

    $(document).ready(function() {
        $('#dataTable').DataTable({
            pagingType: 'full_numbers',
            "aLengthMenu": [
                [5, 10, , 20, 50, 75, -1],
                [5, 10, 20, 50, 75, "All"]
            ],
            "iDisplayLength": 5,
            "bDestroy": true
        });

        $('#formInsertUpdate').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: 'staff/process_staff.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    var obj = jQuery.parseJSON(response);
                    if (obj.status == 200) {
                        //hide modal
                        $('#staffModal').modal('hide');
                        location.reload();
                    } else {
                        //show error on div with id small
                        $('#small').html(obj.message);
                        alert(obj.message);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

    });
</script>

</html>



<!-- Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="staffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded shadow">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staffModalLabel">ADD NEW STAFF</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="staff/process_staff.php" method="post" id="formInsertUpdate">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="first_name" class="form-label">First Name:</label>
                            <input type="text" class="form-control border border-1 border-primary" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="last_name" class="form-label">Last Name:</label>
                            <input type="text" class="form-control border border-1 border-primary" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="phone_number" class="form-label">Phone Number:</label>
                            <input type="text" class="form-control border border-1 border-primary" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control border border-1 border-primary" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <textarea class="form-control border border-1 border-primary" id="address" name="address" required> </textarea>
                    </div>
                    <center> <button type="submit" id='submit' class="btn btn-outline-primary">Add Staff</button> </center>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>