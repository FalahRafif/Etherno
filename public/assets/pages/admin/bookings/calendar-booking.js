(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("booking_calendar_filter_form");
        var calendarElement = document.getElementById("booking_calendar");
        if (!form || !calendarElement) {
            return;
        }

        var eventsUrl = String(form.getAttribute("data-events-url") || "").trim();
        if (eventsUrl === "") {
            return;
        }

        var statusSelect = document.getElementById("calendar_status_filter");
        var dateStartInput = document.getElementById("calendar_date_start");
        var dateEndInput = document.getElementById("calendar_date_end");
        var workflowInput = document.getElementById("calendar_workflow_filter");
        var applyButton = document.getElementById("booking_calendar_apply_filter");
        var resetButton = document.getElementById("booking_calendar_reset_filter");
        var activeFilterBadge = document.getElementById("booking_calendar_active_filter");
        var statusPills = Array.from(document.querySelectorAll(".booking-calendar-status-pill"));
        var workflowChips = Array.from(document.querySelectorAll(".calendar-workflow-chip"));
        var previewPanel = document.getElementById("booking_calendar_preview");
        var previewClose = document.getElementById("calendar_preview_close");
        var previewDetail = document.getElementById("calendar_preview_detail");

        function setText(id, value) {
            var element = document.getElementById(id);
            if (!element) {
                return;
            }

            element.textContent = String(value || "-");
        }

        function getStatusValue() {
            if (!statusSelect) {
                return "";
            }

            return String(statusSelect.value || "").trim();
        }

        function getStatusLabel() {
            if (!statusSelect || !statusSelect.options || statusSelect.selectedIndex < 0) {
                return "Semua Status";
            }

            var option = statusSelect.options[statusSelect.selectedIndex];
            if (!option) {
                return "Semua Status";
            }

            var rawLabel = String(option.textContent || "").trim();
            if (rawLabel === "") {
                return "Semua Status";
            }

            return rawLabel;
        }

        function syncStatusPills() {
            var currentStatus = getStatusValue().toUpperCase();
            statusPills.forEach(function (pill) {
                var code = String(pill.getAttribute("data-status-code") || "").toUpperCase();
                var isActive = code === currentStatus;
                if (code === "" && currentStatus === "") {
                    isActive = true;
                }

                pill.classList.toggle("is-active", isActive);
            });
        }

        function getWorkflowValue() {
            if (!workflowInput) {
                return "";
            }

            return String(workflowInput.value || "").trim();
        }

        function syncWorkflowChips() {
            var currentWorkflow = getWorkflowValue().toLowerCase();
            workflowChips.forEach(function (chip) {
                var key = String(chip.getAttribute("data-workflow-key") || "").toLowerCase();
                chip.classList.toggle("is-active", key === currentWorkflow);
            });
        }

        function updateActiveFilterBadge() {
            if (!activeFilterBadge) {
                return;
            }

            var statusLabel = getStatusLabel();
            var startDate = dateStartInput ? String(dateStartInput.value || "").trim() : "";
            var endDate = dateEndInput ? String(dateEndInput.value || "").trim() : "";

            var rangeLabel = "Semua Tanggal";
            if (startDate !== "" && endDate !== "") {
                rangeLabel = startDate + " s/d " + endDate;
            } else if (startDate !== "") {
                rangeLabel = "Mulai " + startDate;
            } else if (endDate !== "") {
                rangeLabel = "Sampai " + endDate;
            }

            var workflowLabel = "Semua Filter";
            workflowChips.forEach(function (chip) {
                var key = String(chip.getAttribute("data-workflow-key") || "").toLowerCase();
                if (key === getWorkflowValue().toLowerCase()) {
                    var label = chip.querySelector("span");
                    workflowLabel = label ? String(label.textContent || "").trim() : workflowLabel;
                }
            });

            activeFilterBadge.textContent = workflowLabel + " | " + statusLabel + " | " + rangeLabel;
        }

        function getFilters() {
            return {
                status: getStatusValue(),
                workflow: getWorkflowValue(),
                date_start: dateStartInput ? String(dateStartInput.value || "").trim() : "",
                date_end: dateEndInput ? String(dateEndInput.value || "").trim() : ""
            };
        }

        function openPreview(event) {
            if (!previewPanel || !event || !event.extendedProps) {
                return;
            }

            var props = event.extendedProps;
            var detailUrl = String(props.detail_url || "").trim();
            setText("calendar_preview_case", props.case_id || event.title || "-");
            setText("calendar_preview_customer", props.customer_name || "-");
            setText("calendar_preview_status", props.status_label || "-");
            setText("calendar_preview_readiness", props.readiness_label || "-");
            setText("calendar_preview_schedule", [props.event_date_label, props.session_label].filter(Boolean).join(" | "));
            setText("calendar_preview_package", props.package_name || "-");
            setText("calendar_preview_location", props.location_name || "-");
            setText("calendar_preview_risk", String(props.risk_level || "preview").replace(/_/g, " "));

            if (previewDetail) {
                previewDetail.href = detailUrl !== "" ? detailUrl : "#";
                previewDetail.textContent = props.next_action_label || "Buka Detail";
            }

            previewPanel.classList.remove("d-none");
        }

        function closePreview() {
            if (previewPanel) {
                previewPanel.classList.add("d-none");
            }
        }

        if (!window.EthernoFullCalendar || typeof window.EthernoFullCalendar.init !== "function") {
            return;
        }

        var calendarApi = window.EthernoFullCalendar.init({
            calendarElementId: "booking_calendar",
            loadingElementId: "booking_calendar_loading",
            eventsEndpoint: eventsUrl,
            locale: "id",
            getFilters: getFilters,
            onEventNavigate: function (detailUrl, event) {
                openPreview(event);
            },
            onAfterLoad: function () {
                updateActiveFilterBadge();
            }
        });

        if (!calendarApi || typeof calendarApi.refetch !== "function") {
            return;
        }

        if (statusSelect) {
            statusSelect.addEventListener("change", function () {
                syncStatusPills();
            });
        }

        if (applyButton) {
            applyButton.addEventListener("click", function () {
                syncStatusPills();
                updateActiveFilterBadge();
                calendarApi.refetch();
            });
        }

        if (resetButton) {
            resetButton.addEventListener("click", function () {
                if (statusSelect) {
                    statusSelect.value = "";
                }
                if (dateStartInput) {
                    dateStartInput.value = "";
                }
                if (dateEndInput) {
                    dateEndInput.value = "";
                }
                if (workflowInput) {
                    workflowInput.value = "";
                }

                syncStatusPills();
                syncWorkflowChips();
                updateActiveFilterBadge();
                closePreview();
                calendarApi.refetch();
            });
        }

        statusPills.forEach(function (pill) {
            pill.addEventListener("click", function () {
                if (!statusSelect) {
                    return;
                }

                statusSelect.value = String(pill.getAttribute("data-status-code") || "").trim();
                syncStatusPills();
                updateActiveFilterBadge();
                closePreview();
                calendarApi.refetch();
            });
        });

        workflowChips.forEach(function (chip) {
            chip.addEventListener("click", function () {
                if (!workflowInput) {
                    return;
                }

                workflowInput.value = String(chip.getAttribute("data-workflow-key") || "").trim();
                syncWorkflowChips();
                updateActiveFilterBadge();
                closePreview();
                calendarApi.refetch();
            });
        });

        if (previewClose) {
            previewClose.addEventListener("click", closePreview);
        }

        syncStatusPills();
        syncWorkflowChips();
        updateActiveFilterBadge();
    });
})();
