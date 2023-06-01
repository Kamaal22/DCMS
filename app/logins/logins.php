<?php
include_once('./app/database/conn.php')
?>

<div class="container-fluid ">

    <div class=" mt-1 p-3 shadow-lg rounded">
        <div class='small' id='small'></div>
        <div class='d-flex justify-content-between mb-4'>
            <h2 class="text-center text-primary">Employees Login Login Credentials</h2>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#loginModal">
                ADD NEW LOGIN
            </button>
        </div>

        <table class="table table-hover" id="dataTable">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th> Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Select all staff from the database
                $result = mysqli_query($conn, "SELECT * FROM employee_login_view ");

                // Loop through the results and output each staff member as a table row
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['employee_id'] . "</td>";
                    echo "<td>" . $row['first_name'] . "</td>";
                    echo "<td>" . $row['last_name'] . "</>";
                    echo "<td>" . $row['role'] . "</>";
                    echo "<td>" . $row['Username'] . "</td>";
                    echo "<td>" . $row['Password'] . "</td>";
                    echo "<td class='text-center'> 
                                    <button  class='btn btn-primary' onclick='editLogin(" . $row['employee_id'] . ")'> <i class='fa fa-edit'></i> </button> 
                                    <a href='#' class='btn btn-danger ms-2 mt-1' onclick='deleteLogin(" . $row['employee_id'] . ")'> <i class='fa fa-trash'></i> </a> 
                                  </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded shadow">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="loginModalLabel">ADD NEW LOGIN</h1>
            </div>
            <form action="./app/logins/process_login.php" method="post" id="formInsertUpdate">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="mb-3">
                            <!-- select   first_name and last_name from  employees table -->
                            <label for="employee" class="form-label">First Name:</label>
                            <select class="form-control select2 border border-1 border-primary" id="employee" name="employee" required>
                                <option value="">Select Employee</option>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM employees");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['employee_id'] . "'>" . $row['first_name'] . ' '. $row['last_name']  . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 ">
                            <label for="Username" >Username:</label>
                            <input type="text" class="form-control border border-1 border-primary" id="Username" name="Username" required>
                        </div>
                        <div class="mb-3 ">
                            <label for="Password" >Password:</label>
                            <input type="text" class="form-control border border-1 border-primary" id="Password" name="Password" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id='submit' class="btn btn-outline-primary">Add Login</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function editLogin(ids) {

        var id = ids;
        $('#id').val(id);
        $.ajax({
            url: './app/logins/getLogin.php',
            type: 'POST',
            data: {
                updateid: id
            },
            success: function(response) {
                // alert(response);
                var data = JSON.parse(response);
                $('#formInsertUpdate select[name="employee"]').val(data.employee_id).trigger('change');;
                $('#Username').val(data.Username);
                $('#Password').val(data.Password);

            }
        });

        $("#submit").text('Update');
        //toggle modal
        $('#loginModal').modal('show');
    }

    function deleteLogin(id) {
        var id = id;
        $.ajax({
            url: './app/logins/deleteLogin.php',
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

        $(".select2").select2();

        //make the width of the select2 100%
        $('.select2').css('width', '100%');


        
        $('#role').select2({
            dropdownParent: $('#loginModal')
        });
        $('#employee').select2({
            dropdownParent: $('#loginModal')
        });
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
                url: './app/logins/process_login.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    // alert(response);
                    var obj = jQuery.parseJSON(response);
                    if (obj.status == 200) {
                        //hide modal
                        $('#loginModal').modal('hide');
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