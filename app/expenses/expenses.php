<?php
// Connect to the database
include_once('./app/database/conn.php');

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Expenses</title>
</head>

<body>
    <div class="container-fluid ">

        <div class=" mt-1 p-3 shadow overflow-auto rounded  bg-white">
            <div class='small' id='small'></div>
            <div class='d-flex justify-content-between mb-4'>
                <h2 class="text-center text-primary">Expenses</h2>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary me-5" data-toggle="modal" data-target="#ExpenseModal">
                    <i class="fa-solid fa-plus "></i>
                </button>
            </div>
            <table class="table table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th scope="col">#NO</th>
                        <th>Expense Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    // Select all services from the database
                    $sql = "SELECT * FROM expenses_expense_types_view";
                    $result = mysqli_query($conn, $sql);
                    // Loop through each row and display the data in the table
                    while ($row = mysqli_fetch_assoc($result)) {
                        $count++;
                    ?>

                        <tr>
                            <td><?php echo  $count; ?> </td>
                            <td><?php echo   htmlspecialchars ($row["expense_type"]); ?></td>
                            <td><?php echo   htmlspecialchars ($row["description"]); ?></td>
                            <td><?php echo   htmlspecialchars ($row["amount"]); ?></td>
                            <td><?php echo   htmlspecialchars ($row["quantity"]); ?></td>
                            <td><?php echo  $row["date"]; ?></td>
                            <td class='text-center'>
                                <button class='btn btn-primary' onclick='editExpenses(<?php echo  $row["expense_id"]; ?>)'> <i class='fa fa-edit'></i> </button>
                                <a href='#' class='btn btn-danger ms-2' onclick='deleteExpense( <?php echo  $row["expense_id"]; ?> )'> <i class='fa fa-trash'></i> </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="ExpenseModal" aria-labelledby="ExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-4" id="ExpenseModalLabel">New Expenses</h1>
                </div>
                <form id="formInsertUpdate">
                    <div class="modal-body">

                        <!-- hidden input -->
                        <input type="hidden" name="id" id="id">

                        <div class="mb-3">
                            <label for="name" class="form-label text-primary ">Expenses Type</label> <br>
                            <select class="form-control select2 " id="expense_type" name="expense_type" REQUIRED>
                                <option value="">Select Expense Type</option>
                                <?php
                                $query = "SELECT * FROM `expense_types`";
                                $result = mysqli_query($conn, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<option value='" . $row['expense_type_id'] . "'>" . $row['expense_type'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label text-primary">Description</label>
                            <textarea class="form-control border border-1 border-primary" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fee" class="form-label text-primary">Amount</label>
                            <input type="number" class="form-control border border-1 border-primary" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="fee" class="form-label text-primary">Quantity</label>
                            <input type="number" class="form-control border border-1 border-primary" id="Quantity" name="Quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label text-primary ">Expense Date</label>
                            <input type="date" class="form-control border border-1 border-primary" id="date" name="date" required>
                        </div>
                        <div class="text-center">

                        </div>

                    </div>
                    <!-- modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeButton">Close</button>
                        <button type="Button" class="btn btn-outline-primary" onclick="insertUpdate()" id="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    function editExpenses(ids) {
        var id = ids;

        $('#id').val(id);
        showLoader();
        $.ajax({
            url: './app/expenses/getExpenses.php',
            type: 'POST',
            data: {
                updateid: id
            },
            success: function(response) {
                var data = JSON.parse(response);
                $('#date').val(data.date);
                $('#description').val(data.description);
                $('#amount').val(data.amount);
                $('#Quantity').val(data.quantity);
                $('#expense_type').val(data.expense_type);
                $('#formInsertUpdate select[name="expense_type"]').val(data.expense_type).trigger('change');

                hideLoader();
            },
            error: function(data) {
                alert(data);
                hideLoader();
            },
            complete: function(data) {
                hideLoader();
            }

        });

        $("#submit").text('Update');
        $(document).ready(function() {
            $('#closeButton').on('click', function() {
                // Close the modal
                $('#ExpenseModal').modal('hide');
            });
            // Show the modal
            $('#ExpenseModal').modal('show');
        });
    }

    function deleteExpense(id) {
        var id = id;
        showLoader();
        $.ajax({
            url: './app/expenses/deleteExpense.php',
            type: 'POST',
            data: {
                deleteid: id
            },
            success: function(response) {
                var obj = jQuery.parseJSON(response);
                if (obj.status == 200) {
                    location.reload();
                    hideLoader();
                } else {
                    alert(obj.message);
                    hideLoader();
                }
            },
            error: function(data) {
                alert(data);
                hideLoader();
            },
            complete: function(data) {
                hideLoader();
            }
        });
    }


    $(document).ready(function() {

        hideLoader();
        $(".select2").select2();

        //make the width of the select2 100%
        $('.select2').css('width', '100%');


        $('#expense_type').select2({
            dropdownParent: $('#ExpenseModal')
        });

        $('#expense_type').select2({
            dropdownParent: $('#ExpenseModal')
        });

        $('#dataTable').DataTable();

    });

    function insertUpdate() {
        $("#submit").text('Update');
        var id = $('#id').val();
        var date = $('#date').val();
        var description = $('#description').val();
        var amount = $('#amount').val();
        var Quantity = $('#Quantity').val();
        var expense_type = $('#expense_type').val();
        // var expense_type = $('#expense_type').val();
        showLoader();
        $.ajax({
            url: './app/expenses/process_expense.php',
            type: 'POST',
            data: {
                id: id,
                date: date,
                description: description,
                amount: amount,
                Quantity: Quantity,
                // expense_type: expense_type,
                expense_type: expense_type
            },
            success: function(response) {
                // alert(response);
                var obj = jQuery.parseJSON(response);
                if (obj.status == 200) {
                    location.reload();
                    hideLoader();
                } else {
                    alert(obj.message);
                    hideLoader();
                }
            },
            error: function(data) {
                alert(data);
                hideLoader();
            },
            complete: function(data) {
                hideLoader();
            }
        });
    }
</script>