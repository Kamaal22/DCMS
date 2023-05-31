<!DOCTYPE html>
<html>

<head>
    <title>Employees Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    include_once('./app/database/conn.php');
    ?>
</head>

<body>

    <div class="container-fluid ">

        <div class=" mt-1 p-3 shadow-lg rounded">
            <div class='small' id='small'></div>
            <div class='d-flex justify-content-between mb-4'>
                <h2 class="text-center text-primary">Employees List</h2>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#employeeModal">
                    ADD Employees
                </button>
            </div>

            <table class="table table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th scope="col">Employee ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Address</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Hire Date</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Select all staff from the database
                    $result = mysqli_query($conn, "SELECT * FROM Employees");

                    // Loop through the results and output each patients member as a table row
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['employee_id'] . "</td>";
                        echo "<td>" . $row['first_name'] . "</td>";
                        echo "<td>" . $row['last_name'] . "</>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['phone'] . "</td>";
                        echo "<td>" . $row['address'] . "</td>";
                        echo "<td>" . $row['gender'] . "</td>";
                        echo "<td>" . $row['hire_date'] . "</td>";
                        echo "<td class='text-center'> 
                                    <button  class='btn btn-primary' onclick='editEmployee(" . $row['employee_id'] . ")'> <icon class='fa fa-edit'></icon> </button> 
                                    <a href='#' class='btn btn-danger ms-2 mt-1' onclick='deleteEmployee(" . $row['employee_id'] . ")'> <icon class='fa fa-trash'></icon> </a> 
                                  </td>";
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>


</html>



<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded shadow">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="employeeModalLabel">ADD NEW Employee</h1>
            </div>
            <form action="./app/patient/process_patient.php" method="post" id="formInsertUpdate">
                <div class="modal-body">
                    <p class='small text-danger' id='small'></p>
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
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control border border-1 border-primary" id="email" name="email" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="phone_number" class="form-label">Phone Number:</label>
                            <input type="text" class="form-control border border-1 border-primary" id="phone_number" name="phone_number" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <textarea class="form-control border border-1 border-primary" id="address" name="address" required> </textarea>
                        </div>
                        <div class="mb-3">
                            <label for='gender'class="form-label">Select a Gender</label>
                            <select class="form-control border border-1 border-primary" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            <label for="Hire Date" class='form-label'>Hire Date</label>
                            <input type="date" class="form-control border border-1 border-primary" id="hire_date" name="hire_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id='submit' class="btn btn-outline-primary">Add Employee</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>




<script>
    function editEmployee(ids) {

        var id = ids;
        $('#id').val(id);
        $.ajax({
            url: './app/employees/getEmployee.php',
            type: 'POST',
            data: {
                updateid: id
            },
            success: function(response) {
                // alert(response)
                var data = JSON.parse(response);
                $('#first_name').val(data.first_name);
                $('#last_name').val(data.last_name);
                $('#phone_number').val(data.phone);
                $('#gender').val(data.gender);
                $('#email').val(data.email);
                $('#address').val(data.address);
                $('#hire_date').val(data.hire_date);
            }

        });

        $("#submit").text('Update');
        //toggle modal
        $('#employeeModal').modal('show');
    }

    function deleteEmployee(id) {
        var id = id;
        $.ajax({
            url: './app/employees/deleteEmployee.php',
            type: 'POST',
            data: {
                deleteid: id
            },
            success: function(response) {
                // alert(response)
                $('#small').text(response);
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
                url: './app/employees/process_employee.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    // alert(response)
                    var obj = jQuery.parseJSON(response);
                    if (obj.status == 200) {
                        //hide modal
                        $('#staffModal').modal('hide');
                        location.reload();
                    } else {
                        //show error on div with id small
                        $('#small').text(obj.message);
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