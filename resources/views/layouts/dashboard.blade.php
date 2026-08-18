@push('styles')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css"
      rel="stylesheet">

<style>

    /* =========================================
       RAISE ISSUE MODAL
    ========================================= */

    .issue-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99990;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(3px);
    }

    .issue-modal-wrapper {
        position: fixed;
        inset: 0;
        z-index: 99991;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 24px;
    }

    .issue-modal {
        width: min(100%, 1000px);

        max-height: calc(100vh - 48px);

        display: flex;
        flex-direction: column;

        overflow: hidden;

        border-radius: 18px;

        background: #fff;

        box-shadow:
            0 25px 50px -12px rgba(0,0,0,.30);
    }

    .issue-modal-header {
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: space-between;

        border-bottom: 1px solid #e2e8f0;

        background: #fff;

        padding: 18px 24px;
    }

    .issue-modal-body {
        flex: 1 1 auto;

        min-height: 0;

        overflow-y: auto;

        overscroll-behavior: contain;

        background: #f8fafc;

        padding: 20px;
    }

    .issue-modal-footer {
        flex-shrink: 0;

        display: flex;
        justify-content: flex-end;

        border-top: 1px solid #e2e8f0;

        background: #fff;

        padding: 14px 24px;
    }

    /* Prevent the AJAX form from becoming another page */

    #issueCreatePopupContent {
        width: 100%;
    }

    /* Select2 */

    .issue-modal .select2-container {
        width: 100% !important;
    }

    .issue-modal .select2-container--default
    .select2-selection--single {

        height: 42px;

        border: 1px solid #cbd5e1;

        border-radius: 8px;

        background: white;
    }

    .issue-modal .select2-selection__rendered {

        line-height: 40px !important;

        padding-left: 12px !important;

        color: #334155 !important;

        font-size: 14px;
    }

    .issue-modal .select2-selection__arrow {

        height: 40px !important;
    }

    .select2-container--open {
        z-index: 100000 !important;
    }

    @media(max-width:640px) {

        .issue-modal-wrapper {
            padding: 10px;
        }

        .issue-modal {
            max-height: calc(100vh - 20px);
            border-radius: 14px;
        }

        .issue-modal-header {
            padding: 14px 16px;
        }

        .issue-modal-body {
            padding: 12px;
        }

        .issue-modal-footer {
            padding: 12px 16px;
        }
    }

</style>

@endpush