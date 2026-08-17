<script>

window.initIssueCreatePopup = function () {

    const form = document.getElementById('issueCreateForm');


    if (!form) {

        console.error(
            'issueCreateForm not found'
        );

        return;

    }


    // Prevent duplicate binding
    if (form.dataset.initialized === 'true') {

        return;

    }


    form.dataset.initialized = 'true';


    form.addEventListener(
        'submit',
        async function (event) {

            event.preventDefault();


            console.log(
                'Issue form submitted'
            );


            clearValidationErrors();


            const submitButton =
                form.querySelector(
                    'button[type="submit"]'
                );


            if (submitButton) {

                submitButton.disabled = true;

                submitButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );

            }


            const formData =
                new FormData(form);


            try {

                const response =
                    await fetch(
                        form.action,
                        {

                            method: 'POST',

                            credentials: 'same-origin',

                            headers: {

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json'

                            },

                            body: formData

                        }
                    );


                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                let data = {};


                if (
                    contentType.includes(
                        'application/json'
                    )
                ) {

                    data =
                        await response.json();

                } else {

                    const text =
                        await response.text();

                    console.error(
                        'Unexpected response:',
                        text
                    );

                    throw new Error(
                        'Server returned an unexpected response.'
                    );

                }


                // =============================================
                // VALIDATION
                // =============================================

                if (
                    response.status === 422
                ) {

                    showValidationErrors(
                        data.errors || {}
                    );


                    showFormMessage(
                        data.message ||
                        'Please correct the highlighted errors.',
                        'error'
                    );


                    return;

                }


                // =============================================
                // SERVER ERROR
                // =============================================

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Unable to create issue.'
                    );

                }


                // =============================================
                // SUCCESS
                // =============================================

                showFormMessage(

                    data.message ||
                    'Issue created successfully.',

                    'success'

                );


                // Show issue number if available

                const successBox =
                    document.getElementById(
                        'issueSuccess'
                    );


                const successIssueNumber =
                    document.getElementById(
                        'successIssueNumber'
                    );


                if (successBox) {

                    successBox.classList.remove(
                        'hidden'
                    );

                }


                if (
                    successIssueNumber &&
                    data.issue_number
                ) {

                    successIssueNumber.textContent =
                        data.issue_number;

                }


                // Reset form

                form.reset();


                // Refresh dashboard and close modal

                setTimeout(
                    function () {

                        const root =
                            document.querySelector(
                                '[x-data="issueDashboard()"]'
                            );


                        if (
                            root &&
                            window.Alpine
                        ) {

                            const component =
                                Alpine.$data(root);


                            component.closeRaiseIssuePopup();

                        }


                        if (
                            typeof window.refreshIssueDashboard ===
                            'function'
                        ) {

                            window.refreshIssueDashboard();

                        }

                    },
                    1200
                );


            } catch (error) {

                console.error(
                    'Issue submit error:',
                    error
                );


                showFormMessage(

                    error.message ||
                    'Something went wrong while submitting the issue.',

                    'error'

                );


            } finally {

                if (submitButton) {

                    submitButton.disabled = false;

                    submitButton.classList.remove(
                        'opacity-60',
                        'cursor-not-allowed'
                    );

                }

            }

        }
    );


    // =========================================================
    // CLEAR VALIDATION
    // =========================================================

    function clearValidationErrors() {

        form.querySelectorAll(
            '[data-error]'
        ).forEach(
            function (element) {

                element.textContent = '';

                element.classList.add(
                    'hidden'
                );

            }
        );


        form.querySelectorAll(
            '.border-red-500'
        ).forEach(
            function (element) {

                element.classList.remove(
                    'border-red-500'
                );

            }
        );


        const validationBox =
            document.getElementById(
                'issueValidationErrors'
            );


        if (validationBox) {

            validationBox.innerHTML = '';

            validationBox.classList.add(
                'hidden'
            );

        }


        const messageBox =
            document.getElementById(
                'issueFormMessage'
            );


        if (messageBox) {

            messageBox.innerHTML = '';

            messageBox.classList.add(
                'hidden'
            );

        }

    }


    // =========================================================
    // VALIDATION ERRORS
    // =========================================================

    function showValidationErrors(errors) {

        const errorBox =
            document.getElementById(
                'issueValidationErrors'
            );


        let html =
            '<ul class="list-disc space-y-1 pl-5">';


        Object.keys(errors).forEach(
            function (field) {

                const messages =
                    errors[field];


                const errorElement =
                    form.querySelector(
                        `[data-error="${field}"]`
                    );


                if (errorElement) {

                    errorElement.innerHTML =
                        messages
                            .map(escapeHtml)
                            .join('<br>');

                    errorElement.classList.remove(
                        'hidden'
                    );

                }


                const input =
                    form.querySelector(
                        `[name="${field}"]`
                    );


                if (input) {

                    input.classList.add(
                        'border-red-500'
                    );

                }


                messages.forEach(
                    function (message) {

                        html +=
                            `<li>${escapeHtml(message)}</li>`;

                    }
                );

            }
        );


        html += '</ul>';


        if (errorBox) {

            errorBox.innerHTML =
                '<strong>Please correct the following errors:</strong>' +
                html;

            errorBox.classList.remove(
                'hidden'
            );

        }

    }


    // =========================================================
    // MESSAGE
    // =========================================================

    function showFormMessage(
        message,
        type
    ) {

        const box =
            document.getElementById(
                'issueFormMessage'
            );


        if (!box) {

            return;

        }


        box.className =
            'mb-4 rounded-xl border px-4 py-3 text-sm font-semibold';


        if (type === 'success') {

            box.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700'
            );

        } else {

            box.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );

        }


        box.textContent =
            message;


        box.classList.remove(
            'hidden'
        );

    }


    // =========================================================
    // ESCAPE HTML
    // =========================================================

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;

    }

};

</script>