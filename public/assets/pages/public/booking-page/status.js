(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("booking_status_lookup_form");
        if (!form) {
            return;
        }

        var lookupUrl = String(form.getAttribute("data-status-lookup-url") || "").trim();
        var bookingCodeInput = document.getElementById("booking_code");
        var lookupError = document.getElementById("booking_status_lookup_error");
        var openVerifyButton = document.getElementById("booking_status_lookup_button");

        var modal = document.getElementById("booking_status_verify_modal");
        var modalCloseButtons = Array.from(document.querySelectorAll("[data-booking-status-verify-close]"));
        var verifyInput = document.getElementById("booking_status_phone_last4");
        var verifySubmitButton = document.getElementById("booking_status_verify_submit");
        var verifyError = document.getElementById("booking_status_verify_error");

        var statusResultPanel = document.getElementById("booking_status_result");
        var statusState = document.getElementById("booking_status_state");
        var statusStateLabel = document.getElementById("booking_status_state_label");
        var statusStateSubtitle = document.getElementById("booking_status_state_subtitle");

        var downloadProofLink = document.getElementById("status_download_proof");
        var historyList = document.getElementById("booking_status_history");
        var mapsPinLink = document.getElementById("status_google_maps_pin_link");

        var isSubmitting = false;
        var lastFocusedElement = null;

        function setText(id, value) {
            var node = document.getElementById(id);
            if (!node) {
                return;
            }

            var normalized = String(value || "").trim();
            node.textContent = normalized !== "" ? normalized : "-";
        }

        function setLookupError(message) {
            if (!lookupError) {
                return;
            }

            var normalized = String(message || "").trim();
            lookupError.textContent = normalized;
            lookupError.hidden = normalized === "";
        }

        function setVerifyError(message) {
            if (!verifyError) {
                return;
            }

            var normalized = String(message || "").trim();
            verifyError.textContent = normalized;
            verifyError.hidden = normalized === "";
        }

        function sanitizeLastFour(value) {
            var digitsOnly = String(value || "").replace(/\D+/g, "");
            return digitsOnly.slice(0, 4);
        }

        function normalizeTone(tone) {
            var normalizedTone = String(tone || "").toLowerCase();
            if (["success", "warning", "info", "danger", "neutral"].indexOf(normalizedTone) !== -1) {
                return normalizedTone;
            }

            return "neutral";
        }

        function openVerifyModal() {
            if (!modal || !verifyInput) {
                return;
            }

            modal.hidden = false;
            document.body.classList.add("booking-confirm-open");
            verifyInput.value = "";
            setVerifyError("");
            verifySubmitButton.disabled = false;
            verifySubmitButton.textContent = "Verifikasi & Tampilkan";
            lastFocusedElement = document.activeElement;
            verifyInput.focus();
        }

        function closeVerifyModal() {
            if (!modal) {
                return;
            }

            modal.hidden = true;
            document.body.classList.remove("booking-confirm-open");
            if (lastFocusedElement && typeof lastFocusedElement.focus === "function") {
                lastFocusedElement.focus();
            }
        }

        function buildRequestUrl(params) {
            var requestUrl = new URL(lookupUrl, window.location.origin);
            Object.keys(params).forEach(function (key) {
                requestUrl.searchParams.set(key, params[key]);
            });

            return requestUrl.toString();
        }

        function renderHistory(items) {
            if (!historyList) {
                return;
            }

            historyList.innerHTML = "";
            if (!Array.isArray(items) || items.length === 0) {
                var emptyItem = document.createElement("li");
                emptyItem.className = "booking-status-history-item";
                emptyItem.textContent = "Riwayat status belum tersedia.";
                historyList.appendChild(emptyItem);
                return;
            }

            items.forEach(function (item) {
                var li = document.createElement("li");
                li.className = "booking-status-history-item";

                var statusSpan = document.createElement("span");
                statusSpan.className = "booking-status-history-status";
                statusSpan.textContent = String(item && item.status ? item.status : "-");

                var timeSpan = document.createElement("span");
                timeSpan.className = "booking-status-history-time";
                timeSpan.textContent = String(item && item.time ? item.time : "-");

                li.appendChild(statusSpan);
                li.appendChild(timeSpan);
                historyList.appendChild(li);
            });
        }

        function renderPayload(payload) {
            var status = payload && typeof payload.status === "object" ? payload.status : {};
            var customer = payload && typeof payload.customer === "object" ? payload.customer : {};
            var eventData = payload && typeof payload.event === "object" ? payload.event : {};
            var packageData = payload && typeof payload.package === "object" ? payload.package : {};
            var billing = payload && typeof payload.billing === "object" ? payload.billing : null;

            var tone = normalizeTone(status.tone);
            if (statusState) {
                statusState.classList.remove("success", "warning", "info", "danger", "neutral");
                statusState.classList.add(tone);
            }

            if (statusStateLabel) {
                statusStateLabel.textContent = "Status: " + String(status.label || "-");
            }
            if (statusStateSubtitle) {
                statusStateSubtitle.textContent = "Diajukan pada " + String(eventData.submitted_at || "-");
            }

            setText("status_case_id", payload.booking_case_id);
            setText("status_request_code", payload.request_code);
            setText("status_customer_name", customer.name);
            setText("status_customer_phone", customer.phone_masked);
            setText("status_event_date", eventData.date_label);
            setText("status_event_session", eventData.session);
            setText("status_package_name", packageData.name);
            setText("status_package_type", packageData.type);
            setText("status_package_case_id", packageData.case_id);
            setText("status_package_price", packageData.price);
            setText("status_package_address", packageData.address);
            setText("status_location", payload.location_label);
            setText("status_event_detail", eventData.detail);

            var mapsPinUrl = String(payload.google_maps_pin || "").trim();
            if (mapsPinLink) {
                if (mapsPinUrl !== "" && mapsPinUrl !== "-") {
                    mapsPinLink.href = mapsPinUrl;
                    mapsPinLink.textContent = "Lihat pin lokasi";
                    mapsPinLink.removeAttribute("aria-disabled");
                } else {
                    mapsPinLink.href = "#";
                    mapsPinLink.textContent = "Pin lokasi belum tersedia";
                    mapsPinLink.setAttribute("aria-disabled", "true");
                }
            }

            if (billing) {
                setText("status_billing_status", "Status pembayaran: " + String(billing.status || "-"));
                setText("status_billing_total", "Total tagihan: " + String(billing.total || "-"));
                setText("status_billing_paid", "Total dibayar: " + String(billing.paid || "-"));
                setText("status_billing_remaining", "Sisa pembayaran: " + String(billing.remaining || "-"));
            } else {
                setText("status_billing_status", "Belum ada data pembayaran.");
                setText("status_billing_total", "Total tagihan: -");
                setText("status_billing_paid", "Total dibayar: -");
                setText("status_billing_remaining", "Sisa pembayaran: -");
            }

            renderHistory(payload.history);

            if (downloadProofLink) {
                var proofUrl = String(payload.proof_download_url || "").trim();
                if (proofUrl !== "") {
                    downloadProofLink.href = proofUrl;
                    downloadProofLink.hidden = false;
                } else {
                    downloadProofLink.href = "#";
                    downloadProofLink.hidden = true;
                }
            }

            if (statusResultPanel) {
                statusResultPanel.hidden = false;
                statusResultPanel.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        }

        function openVerifyIfValid() {
            setLookupError("");
            if (typeof form.reportValidity === "function" && !form.reportValidity()) {
                return;
            }

            openVerifyModal();
        }

        function closeOnEscape(event) {
            if (event.key !== "Escape" || !modal || modal.hidden) {
                return;
            }

            closeVerifyModal();
        }

        function submitLookup() {
            if (isSubmitting || !bookingCodeInput || !verifyInput) {
                return;
            }

            var bookingCode = String(bookingCodeInput.value || "").trim();
            var phoneLast4 = sanitizeLastFour(verifyInput.value);
            verifyInput.value = phoneLast4;
            setVerifyError("");
            setLookupError("");

            if (phoneLast4.length !== 4) {
                setVerifyError("Masukkan tepat 4 digit terakhir nomor WhatsApp.");
                verifyInput.focus();
                return;
            }

            if (lookupUrl === "") {
                setVerifyError("Endpoint cek status belum tersedia.");
                return;
            }

            isSubmitting = true;
            verifySubmitButton.disabled = true;
            verifySubmitButton.textContent = "Memverifikasi...";

            fetch(buildRequestUrl({
                booking_code: bookingCode,
                phone_last4: phoneLast4
            }), {
                method: "GET",
                credentials: "same-origin",
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
                .then(function (response) {
                    return response.json()
                        .catch(function () {
                            return {};
                        })
                        .then(function (payload) {
                            if (!response.ok) {
                                var message = String(payload.message || "Data booking tidak ditemukan atau verifikasi tidak sesuai.");
                                throw new Error(message);
                            }

                            return payload;
                        });
                })
                .then(function (payload) {
                    closeVerifyModal();
                    renderPayload(payload);
                })
                .catch(function (error) {
                    var message = error instanceof Error ? error.message : "Terjadi kendala saat mengambil data booking.";
                    setVerifyError(message);
                })
                .finally(function () {
                    isSubmitting = false;
                    verifySubmitButton.disabled = false;
                    verifySubmitButton.textContent = "Verifikasi & Tampilkan";
                });
        }

        if (bookingCodeInput) {
            bookingCodeInput.addEventListener("input", function () {
                setLookupError("");
            });
        }

        if (verifyInput) {
            verifyInput.addEventListener("input", function () {
                verifyInput.value = sanitizeLastFour(verifyInput.value);
                setVerifyError("");
            });

            verifyInput.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    submitLookup();
                }
            });
        }

        modalCloseButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                closeVerifyModal();
            });
        });

        if (openVerifyButton) {
            openVerifyButton.addEventListener("click", function (event) {
                event.preventDefault();
                openVerifyIfValid();
            });
        }

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            openVerifyIfValid();
        });

        if (verifySubmitButton) {
            verifySubmitButton.addEventListener("click", submitLookup);
        }

        document.addEventListener("keydown", closeOnEscape);
    });
})();
