<?php
    // Start the session
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container">
        <h2>Contact List</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Contact ID</th>
                    <th>User ID</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Contact Date</th>
                    <th>Response</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="contactTableBody">
                <!-- Contact data will be inserted here dynamically -->
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Modal for View Contact Details -->
    <div class="modal fade" id="viewContactModal" tabindex="-1" role="dialog" aria-labelledby="viewContactModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewContactModalLabel">Contact Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="contactDetails">
                        <!-- Contact details will be inserted here dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Edit Contact -->
    <div class="modal fade" id="editContactModal" tabindex="-1" role="dialog" aria-labelledby="editContactModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContactModalLabel">Edit Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editContactForm">
                        <div class="form-group">
                            <label for="response">Response:</label>
                            <textarea class="form-control" id="response" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status">
                                <option value="solve">Solve</option>
                                <option value="pending">Pending</option>
                                <option value="unsolved">Unsolved</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="submitEditForm()">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Function to fetch and update contact data
            function fetchAndUpdateContacts() {
                $.get("fetch_contacts.php", function (data) {
                    if (data.error) {
                        alert("Error fetching contacts: " + data.error);
                    } else {
                        // Clear existing rows
                        $("#contactTableBody").empty();

                        // Populate the table with contact data
                        data.forEach(function (contact) {
                            var row = "<tr data-contact-id='" + contact.contact_id + "'>";
                            row += "<td>" + contact.contact_id + "</td>";
                            row += "<td>" + contact.user_id + "</td>";
                            row += "<td>" + contact.subject + "</td>";
                            row += "<td>" + contact.message + "</td>";
                            row += "<td>" + contact.contact_date + "</td>";
                            row += "<td>" + (contact.reponse ? contact.reponse : '') + "</td>";
                            row += "<td><select class='statusSelect'>" +
                                "<option value='solve'>Solve</option>" +
                                "<option value='pending'>Pending</option>" +
                                "<option value='unsolved'>Unsolved</option>" +
                                "</select></td>";
                            row += "<td>" +
                                "<button class='btn btn-primary' onclick='viewContact(" + contact.contact_id + ")'>View</button>" +
                                "<button class='btn btn-warning' onclick='editContact(" + contact.contact_id + ")'>Edit</button>" +
                                "<button class='btn btn-success' onclick='messageContact(" + contact.contact_id + ", " + contact.user_id + ")'>Message</button>" +
                                "</td>";
                            row += "</tr>";
                            $("#contactTableBody").append(row);

                            // Set the initial status value for each row
                            $("tr:last .statusSelect").val(contact.status);
                        });
                    }
                });
            }

            // Initial fetch and update
            fetchAndUpdateContacts();

            // Function to handle the "View" button click
            window.viewContact = function (contactId) {
                // Find the row corresponding to the clicked contactId
                var clickedRow = $("tr[data-contact-id='" + contactId + "']");

                // Extract Subject, Message, and Contact Date from the clicked row
                var subject = clickedRow.find("td:nth-child(3)").text();
                var message = clickedRow.find("td:nth-child(4)").text();
                var contactDate = clickedRow.find("td:nth-child(5)").text();

                // Build HTML for contact details
                var contactDetailsHtml = "<p><strong>Subject:</strong> " + subject + "</p>";
                contactDetailsHtml += "<p><strong>Message:</strong> " + message + "</p>";
                contactDetailsHtml += "<p><strong>Contact Date:</strong> " + contactDate + "</p>";

                // Update modal content with contact details
                $("#contactDetails").html(contactDetailsHtml);

                // Show the modal
                $("#viewContactModal").modal("show");
            };

            // Function to handle the "Edit" button click
            window.editContact = function (contactId) {
                // Find the row corresponding to the clicked contactId
                var clickedRow = $("tr[data-contact-id='" + contactId + "']");

                // Extract Response and Status from the clicked row
                var response = clickedRow.find("td:nth-child(6)").text();
                var status = clickedRow.find(".statusSelect").val();

                // Set the initial values in the modal form
                $("#editContactModal #response").val(response);
                $("#editContactModal #status").val(status);

                // Set the contact_id data attribute for the modal
                $("#editContactModal").data("contact-id", contactId);

                // Show the modal
                $("#editContactModal").modal("show");
            };

            window.submitEditForm = function () {
                // Get values from the form
                var responseValue = $("#editContactModal #response").val();
                var statusValue = $("#editContactModal #status").val();

                // Get the contact_id from the modal data attribute
                var contactId = $("#editContactModal").data("contact-id");

                // Close the modal
                $("#editContactModal").modal("hide");

                // Make an asynchronous request to update the contact
                $.ajax({
                    type: "POST",
                    url: "fetch_contacts.php",  // Updated endpoint for updating contact
                    data: {
                        contact_id: contactId,
                        response: responseValue,
                        status: statusValue
                    },
                    success: function (response) {
                        // Log the response (you can remove this in production)
                        console.log("Received response:", response);

                        if (response.success) {
                            alert("Contact updated successfully");

                            // Update the row in the table with the new response text
                            $("tr[data-contact-id='" + contactId + "'] td:nth-child(6)").text(responseValue);
                        } else {
                            alert("Error updating contact: " + response.error);
                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error("Error updating contact:", textStatus, errorThrown);
                        console.log("Full xhr object:", xhr); // Add this line to log the full xhr object

                        alert("Error updating contact. Please try again.");
                    }
                });
            };

            window.messageContact = function (contactId, userId) {
                // Find the row corresponding to the clicked contactId
                var clickedRow = $("tr[data-contact-id='" + contactId + "']");

                // Extract Subject from the clicked row
                var subject = clickedRow.find("td:nth-child(3)").text();

                // Trigger a request to fetch user email
                $.get("fetch_user_email.php", { user_id: userId }, function (response) {
                    if (response.success) {
                        // Open the default email client with the pre-filled email and subject
                        var mailtoLink = "mailto:" + response.email + "?subject=" + encodeURIComponent(subject);
                        window.location.href = mailtoLink;
                    } else {
                        alert("Error fetching user email: " + response.error);
                    }
                });
            };
        });
    </script>
</body>

</html>
