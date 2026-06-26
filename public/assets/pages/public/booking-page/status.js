(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("booking_status_lookup_form");
        if (!form) return;

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

        var uploadPaymentModal = document.getElementById("upload_payment_modal");
        var uploadPaymentForm = document.getElementById("upload_payment_form");
        var uploadPaymentCloseButtons = Array.from(document.querySelectorAll("[data-upload-payment-close]"));
        var uploadPaymentError = document.getElementById("upload_payment_error");
        var uploadPaymentSubmitBtn = document.getElementById("upload_payment_submit_btn");
        var uploadPaymentAmountInfo = document.getElementById("upload_payment_amount_info");
        var uploadPaymentInstallmentId = document.getElementById("upload_payment_installment_id");

        var rescheduleModal = document.getElementById("reschedule_request_modal");
        var rescheduleForm = document.getElementById("reschedule_request_form");
        var rescheduleCloseButtons = Array.from(document.querySelectorAll("[data-reschedule-request-close]"));
        var rescheduleError = document.getElementById("reschedule_request_error");
        var rescheduleSuccess = document.getElementById("reschedule_request_success");
        var rescheduleSubmitBtn = document.getElementById("reschedule_request_submit_btn");

        var isSubmitting = false;
        var lastFocusedElement = null;
        var currentPayload = null;

        function setText(id, value) {
            var node = document.getElementById(id);
            if (!node) return;
            var normalized = String(value || "").trim();
            node.textContent = normalized !== "" ? normalized : "-";
        }

        function setLookupError(message) {
            if (!lookupError) return;
            var normalized = String(message || "").trim();
            lookupError.textContent = normalized;
            lookupError.hidden = normalized === "";
        }

        function setVerifyError(message) {
            if (!verifyError) return;
            var normalized = String(message || "").trim();
            verifyError.textContent = normalized;
            verifyError.hidden = normalized === "";
        }

        function sanitizeLastFour(value) {
            return String(value || "").replace(/\D+/g, "").slice(0, 4);
        }

        function normalizeTone(tone) {
            var t = String(tone || "").toLowerCase();
            return ["success", "warning", "info", "danger", "neutral"].indexOf(t) !== -1 ? t : "neutral";
        }

        function openVerifyModal() {
            if (!modal || !verifyInput) return;
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
            if (!modal) return;
            modal.hidden = true;
            document.body.classList.remove("booking-confirm-open");
            if (lastFocusedElement && typeof lastFocusedElement.focus === "function") {
                lastFocusedElement.focus();
            }
        }

        function buildWhatsappUrl(phone, message) {
            var p = String(phone || "").replace(/[^0-9]/g, "");
            if (p === "") return "#";
            if (p.startsWith("0")) p = "62" + p.substring(1);
            return "https://wa.me/" + p + "?text=" + encodeURIComponent(String(message || ""));
        }

        function findLatestHistoryDescription(requiredText, requiredSecondText) {
            var items = currentPayload && Array.isArray(currentPayload.history) ? currentPayload.history : [];
            for (var i = items.length - 1; i >= 0; i -= 1) {
                var description = String(items[i] && items[i].description ? items[i].description : "").trim();
                var normalized = description.toLowerCase();
                if (description !== "" && normalized.indexOf(requiredText) !== -1 && normalized.indexOf(requiredSecondText) !== -1) {
                    return description;
                }
            }
            return "";
        }

        function escHtml(value) {
            return String(value == null ? "" : value)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\"/g, "&quot;")
                .replace(/'/g, "&#39;");
        }

        function renderHistory(items) {
            if (!historyList) return;
            historyList.innerHTML = "";
            if (!Array.isArray(items) || items.length === 0) {
                var emptyItem = document.createElement("li");
                emptyItem.className = "booking-status-timeline-empty";
                emptyItem.textContent = "Riwayat status belum tersedia.";
                historyList.appendChild(emptyItem);
                return;
            }
            items.forEach(function (item) {
                var li = document.createElement("li");
                li.className = "booking-status-timeline-item";

                var marker = document.createElement("span");
                marker.className = "booking-status-timeline-marker";
                marker.setAttribute("aria-hidden", "true");
                li.appendChild(marker);

                var content = document.createElement("div");
                content.className = "booking-status-timeline-content";

                var header = document.createElement("div");
                header.className = "booking-status-timeline-head";

                var statusSpan = document.createElement("span");
                statusSpan.className = "booking-status-timeline-status";
                statusSpan.textContent = String(item && item.status ? item.status : "-");

                var timeSpan = document.createElement("span");
                timeSpan.className = "booking-status-timeline-time";
                timeSpan.textContent = String(item && item.time ? item.time : "-");

                header.appendChild(statusSpan);
                header.appendChild(timeSpan);
                content.appendChild(header);

                var infoSpan = document.createElement("span");
                infoSpan.className = "booking-status-timeline-info-card";
                infoSpan.textContent = String(item && item.info ? item.info : "Informasi status");
                content.appendChild(infoSpan);

                if (item && item.message) {
                    var descWrap = document.createElement("div");
                    descWrap.className = "booking-status-timeline-detail";

                    var descSubject = String(item && item.message_subject ? item.message_subject : "Pesan / alasan");
                    var descLabel = document.createElement("span");
                    descLabel.className = "booking-status-timeline-detail-label";
                    descLabel.textContent = descSubject;

                    var descSpan = document.createElement("span");
                    descSpan.className = "booking-status-timeline-desc";
                    descSpan.textContent = item.message;

                    descWrap.appendChild(descLabel);
                    descWrap.appendChild(descSpan);
                    content.appendChild(descWrap);
                }

                li.appendChild(content);
                historyList.appendChild(li);
            });
        }

        function renderBillingDetails(details) {
            var wrap = document.getElementById("billing_details_wrap");
            var list = document.getElementById("billing_details_list");
            if (!wrap || !list) return;
            list.innerHTML = "";
            if (!Array.isArray(details) || details.length === 0) {
                wrap.hidden = true;
                return;
            }
            wrap.hidden = false;
            var grid = document.createElement("div");
            grid.className = "billing-detail-list";
            details.forEach(function (d) {
                var item = document.createElement("div");
                item.className = "billing-detail-row";
                var textWrap = document.createElement("div");
                var k = document.createElement("p");
                k.className = "billing-detail-name";
                k.textContent = String(d.name || "-");
                var sub = document.createElement("p");
                sub.className = "billing-detail-meta";
                sub.textContent = String(d.type || "-");
                textWrap.appendChild(k);
                textWrap.appendChild(sub);
                var v = document.createElement("p");
                v.className = "billing-detail-amount";
                v.textContent = String(d.amount_label || "-");
                item.appendChild(textWrap);
                item.appendChild(v);
                grid.appendChild(item);
            });
            list.appendChild(grid);
        }

        function renderBillingOverview(billing) {
            var badge = document.getElementById("billing_overview_status_badge");
            var refundCard = document.getElementById("billing_refund_summary_card");
            if (!badge || !refundCard) return;

            var statusCode = String((billing && billing.status_code) || "");
            var statusLabel = String((billing && billing.status) || "Belum ada billing");
            var refundAmount = Number((billing && billing.refunded) ? String(billing.refunded).replace(/[^0-9]/g, "") : 0);
            badge.textContent = statusLabel;
            badge.className = "billing-overview-badge";
            if (statusCode === "BLS_PAID") badge.classList.add("is-success");
            else if (statusCode === "BLS_PARTIAL") badge.classList.add("is-warning");
            else if (statusCode === "BLS_UNPAID") badge.classList.add("is-neutral");
            else badge.classList.add("is-info");

            if (refundAmount > 0) {
                refundCard.hidden = false;
            } else {
                refundCard.hidden = true;
            }
        }

        function renderPrimaryCharges(installments) {
            var wrap = document.getElementById("billing_primary_charges_wrap");
            var list = document.getElementById("billing_primary_charges_list");
            if (!wrap || !list) return;
            list.innerHTML = "";

            var mainCharges = Array.isArray(installments) ? installments.filter(function (inst) {
                return String(inst.type_code || "") !== "INS_REFUND";
            }) : [];

            if (!mainCharges.length) {
                wrap.hidden = true;
                return;
            }

            wrap.hidden = false;
            var grid = document.createElement("div");
            grid.className = "billing-charge-grid";

            mainCharges.forEach(function (inst) {
                var card = document.createElement("article");
                card.className = "billing-charge-card";
                card.innerHTML = ''
                    + '<div class="billing-charge-head"><p class="billing-charge-title">' + escHtml(inst.type || "-") + '</p><span class="billing-charge-badge">' + escHtml(inst.status || "-") + '</span></div>'
                    + '<p class="billing-charge-amount">' + escHtml(inst.amount_label || "-") + '</p>'
                    + '<div class="billing-charge-meta"><span>Terbayar: ' + escHtml(inst.paid_label || "-") + '</span><span>Sisa: ' + escHtml(inst.remaining_label || "-") + '</span></div>'
                    + '<div class="billing-charge-meta"><span>Jatuh tempo: ' + escHtml(inst.due_date || "-") + '</span><span>' + ((inst.payments || []).length) + ' transaksi</span></div>';
                grid.appendChild(card);
            });

            list.appendChild(grid);
        }

        function renderRefunds(installments) {
            var wrap = document.getElementById("billing_refund_wrap");
            var list = document.getElementById("billing_refund_list");
            if (!wrap || !list) return;
            list.innerHTML = "";

            var refunds = Array.isArray(installments) ? installments.filter(function (inst) {
                return String(inst.type_code || "") === "INS_REFUND";
            }) : [];

            if (!refunds.length) {
                wrap.hidden = true;
                return;
            }

            wrap.hidden = false;
            refunds.forEach(function (inst) {
                var card = document.createElement("article");
                card.className = "billing-refund-card";
                card.innerHTML = ''
                    + '<div class="billing-charge-head"><p class="billing-charge-title">Refund</p><span class="billing-charge-badge">' + escHtml(inst.status || "-") + '</span></div>'
                    + '<p class="billing-charge-amount">' + escHtml(inst.amount_label || "-") + '</p>'
                    + '<div class="billing-charge-meta"><span>Sudah direfund: ' + escHtml(inst.paid_label || "-") + '</span><span>Sisa refund: ' + escHtml(inst.remaining_label || "-") + '</span></div>'
                    + '<div class="billing-charge-meta"><span>Target proses: ' + escHtml(inst.due_date || "-") + '</span><span>' + ((inst.payments || []).length) + ' transaksi</span></div>';
                list.appendChild(card);
            });
        }

        function renderInstallments(installments) {
            var wrap = document.getElementById("billing_installments_wrap");
            var list = document.getElementById("billing_installments_list");
            if (!wrap || !list) return;
            list.innerHTML = "";
            if (!Array.isArray(installments) || installments.length === 0) {
                wrap.hidden = true;
                return;
            }
            wrap.hidden = false;
            var tableWrap = document.createElement("div");
            tableWrap.className = "booking-history-table-wrap";
            var table = document.createElement("table");
            table.className = "booking-history-table";
            table.innerHTML = '<thead><tr><th>Tagihan</th><th>Status</th><th>Nominal</th><th>Terbayar</th><th>Jatuh Tempo</th><th>Lampiran</th><th>Update Terakhir</th></tr></thead>';
            var tbody = document.createElement("tbody");
            var totals = {
                accumulationAmount: 0,
                paymentAmount: 0,
                refundPaid: 0
            };

            installments.forEach(function (inst) {
                var payments = Array.isArray(inst && inst.payments) ? inst.payments : [];
                var latestPayment = payments.length ? payments[payments.length - 1] : null;
                var statusLabel = String((inst && inst.status) || "-");
                var latestUpdate = latestPayment ? String(latestPayment.paid_at || "-") : "Belum ada pembayaran";
                var latestReceiptUrl = latestPayment && latestPayment.receipt_url ? String(latestPayment.receipt_url) : "";
                var typeCode = String((inst && inst.type_code) || "");
                var isRefund = typeCode === "INS_REFUND";

                if (isRefund) {
                    totals.refundPaid += Number((inst && inst.paid_amount) || 0);
                } else {
                    totals.accumulationAmount += Number((inst && inst.amount) || 0);
                    totals.paymentAmount += Number((inst && inst.paid_amount) || 0);
                }

                var row = document.createElement("tr");
                if (Number((inst && inst.paid_amount) || 0) > 0) {
                    row.className = isRefund ? "booking-history-table-row-paid-refund" : "booking-history-table-row-paid-charge";
                }
                var cells = [
                    { html: '<strong>' + escHtml((inst && inst.type) || "-") + '</strong><div class="booking-history-table-note">' + escHtml(payments.length ? ('Riwayat pembayaran: ' + payments.length + ' transaksi') : 'Belum ada transaksi') + '</div>' },
                    { text: statusLabel },
                    { text: String((inst && inst.amount_label) || "-") },
                    { text: String((inst && inst.paid_label) || "-") },
                    { text: String((inst && inst.due_date) || "-") },
                    { html: latestReceiptUrl ? '<a href="' + escHtml(latestReceiptUrl) + '" target="_blank" rel="noopener">Bukti Pembayaran</a>' : '<span class="booking-history-table-note">-</span>' },
                    { text: latestUpdate }
                ];

                cells.forEach(function (cellData) {
                    var td = document.createElement("td");
                    if (cellData.html) {
                        td.innerHTML = cellData.html;
                    } else {
                        td.textContent = cellData.text;
                    }
                    row.appendChild(td);
                });
                tbody.appendChild(row);

                payments.forEach(function (p) {
                    var pCode = String((p && p.status_code) || "");
                    var paymentStatus = String((p && p.status) || "-");
                    if (pCode === "PYS_PEDING") paymentStatus = "Menunggu Verifikasi";
                    else if (pCode === "PYS_FAILED") paymentStatus = "Ditolak";
                    else if (pCode === "PYS_SUCCESS") paymentStatus = "Terverifikasi";

                    var payRow = document.createElement("tr");
                    payRow.className = "booking-history-table-subrow";

                    var payInfoTd = document.createElement("td");
                    payInfoTd.innerHTML = '<span class="booking-history-table-subtitle">Pembayaran</span><div class="booking-history-table-note">' + escHtml((p && p.method) || "-") + '</div>';
                    if (p && p.rejection_reason) {
                        var reasonDiv = document.createElement("div");
                        reasonDiv.className = "booking-history-table-note text-danger";
                        reasonDiv.textContent = 'Alasan: ' + p.rejection_reason;
                        payInfoTd.appendChild(reasonDiv);
                    }
                    payRow.appendChild(payInfoTd);

                    var payStatusTd = document.createElement("td");
                    payStatusTd.textContent = paymentStatus;
                    payRow.appendChild(payStatusTd);

                    var payAmountTd = document.createElement("td");
                    payAmountTd.textContent = String((p && p.amount_label) || "-");
                    payRow.appendChild(payAmountTd);

                    var paySummaryTd = document.createElement("td");
                    paySummaryTd.colSpan = 1;
                    paySummaryTd.textContent = pCode === "PYS_SUCCESS" ? 'Masuk ke total pembayaran' : '-';
                    payRow.appendChild(paySummaryTd);

                    var payDateTd = document.createElement("td");
                    payDateTd.textContent = String((p && p.paid_at) || "-");
                    payRow.appendChild(payDateTd);
                    
                    var payReceiptTd = document.createElement("td");
                    if (p && p.receipt_url) {
                        var receiptLink = document.createElement("a");
                        receiptLink.href = p.receipt_url;
                        receiptLink.target = "_blank";
                        receiptLink.rel = "noopener";
                        receiptLink.textContent = "Bukti Pembayaran";
                        payReceiptTd.appendChild(receiptLink);
                    } else {
                        payReceiptTd.textContent = '-';
                    }
                    payRow.appendChild(payReceiptTd);
                    var fillEmpty = document.createElement("td");
                    fillEmpty.textContent = '';
                    payRow.appendChild(fillEmpty);


                    tbody.appendChild(payRow);
                });
            });

            table.appendChild(tbody);

            var remainingAmount = Math.max(totals.accumulationAmount - totals.paymentAmount, 0);
            var hasOutstanding = remainingAmount > 0;
            var hasRefund = totals.refundPaid > 0;

            var tfoot = document.createElement("tfoot");
            tfoot.className = "booking-history-table-footer";

            var accumulationRow = document.createElement("tr");
            accumulationRow.className = "booking-history-table-footer-row";
            accumulationRow.innerHTML = ''
                + '<td colspan="2"><strong>Total Akumulasi Tagihan</strong><div class="booking-history-table-note">Total seluruh tagihan utama (DP, pelunasan, dll). Refund tidak ikut dihitung.</div></td>'
                + '<td colspan="5"><strong>' + formatCurrency(totals.accumulationAmount) + '</strong></td>';
            tfoot.appendChild(accumulationRow);

            var paymentRow = document.createElement("tr");
            paymentRow.className = "booking-history-table-footer-row";
            paymentRow.innerHTML = ''
                + '<td colspan="2"><strong>Total Pembayaran Diterima</strong><div class="booking-history-table-note">Total pembayaran yang telah diterima untuk tagihan utama. Refund tidak mengurangi nominal ini.</div></td>'
                + '<td colspan="5"><strong>' + formatCurrency(totals.paymentAmount) + '</strong></td>';
            tfoot.appendChild(paymentRow);

            var remainingRow = document.createElement("tr");
            remainingRow.className = "booking-history-table-footer-row";
            remainingRow.innerHTML = ''
                + '<td colspan="2"><strong>Sisa Tagihan</strong><div class="booking-history-table-note">Total akumulasi tagihan dikurangi total pembayaran diterima.</div></td>'
                + '<td colspan="5"><strong class="' + (hasOutstanding ? 'booking-history-table-amount-danger' : '') + '">' + formatCurrency(remainingAmount) + '</strong></td>';
            tfoot.appendChild(remainingRow);

            var refundRow = document.createElement("tr");
            refundRow.className = "booking-history-table-footer-row";
            refundRow.innerHTML = ''
                + '<td colspan="2"><strong>Total Refund</strong><div class="booking-history-table-note">Total dana yang telah dikembalikan ke customer.</div></td>'
                + '<td colspan="5"><strong class="' + (hasRefund ? 'booking-history-table-amount-success' : '') + '">' + formatCurrency(totals.refundPaid) + '</strong></td>';
            tfoot.appendChild(refundRow);
            table.appendChild(tfoot);
            tableWrap.appendChild(table);
            list.appendChild(tableWrap);
        }

        function formatCurrency(value) {
            var amount = Number(value || 0);
            return "Rp " + amount.toLocaleString("id-ID");
        }

        function renderCustomerActions(actions, billing, waPhone, waTemplates) {
            var wrap = document.getElementById("customer_actions_list");
            if (!wrap) return;
            wrap.innerHTML = "";

            var statusCode = currentPayload && currentPayload.status ? String(currentPayload.status.code || "") : "";
            var isWaitingApproval = statusCode === "BS_WAITING_APPROVAL";
            var isApprovedWaitingDp = statusCode === "BS_APPROVED_WAITING_DP";
            var isApprovedWaitingFinal = statusCode === "BS_APPROVED_WAITING_FINAL_PAYMENT";
            var isConfirmed = statusCode === "BS_CONFIRMED";
            var isReschedule = statusCode === "BS_RESCHEDULE";
            var isForceMajeure = statusCode === "BS_FORCE_MAJEURE";
            var isRefund = statusCode === "BS_REFUND";

            var hasDpInstallment = billing && Array.isArray(billing.installments) && billing.installments.some(function (i) {
                return String(i.type_code || "") === "INS_DP";
            });
            var hasFinalInstallment = billing && Array.isArray(billing.installments) && billing.installments.some(function (i) {
                return String(i.type_code || "") === "INS_FINAL";
            });

            if (isWaitingApproval) {
                var waitingInfoDiv = document.createElement("div");
                waitingInfoDiv.className = "estimate-box mb-3";
                waitingInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-time-line me-1"></i><strong>Pengajuan Sedang Diverifikasi</strong></p><p class="estimate-note mb-0">Data booking Anda sudah kami terima dan sedang dalam proses review oleh tim Etherno. Anda akan dihubungi melalui WhatsApp setelah pengajuan disetujui untuk melanjutkan ke tahap pembayaran DP.</p>';
                wrap.appendChild(waitingInfoDiv);

                if (waPhone && waTemplates && waTemplates.waiting_approval) {
                    var waBtn = document.createElement("a");
                    waBtn.className = "cta mb-2";
                    waBtn.href = buildWhatsappUrl(waPhone, waTemplates.waiting_approval);
                    waBtn.target = "_blank";
                    waBtn.rel = "noopener";
                    waBtn.innerHTML = '<i class="ri-whatsapp-line me-1"></i> Tanyakan Progres via WhatsApp';
                    wrap.appendChild(waBtn);
                }
            }

            if (isApprovedWaitingDp && !hasDpInstallment) {
                var dpPendingInfoDiv = document.createElement("div");
                dpPendingInfoDiv.className = "estimate-box mb-3";
                dpPendingInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-search-line me-1"></i><strong>Menunggu Tagihan DP</strong></p><p class="estimate-note mb-0">Pengajuan booking Anda telah <strong>disetujui</strong>. Tim Etherno sedang melakukan pengecekan harga final termasuk biaya tambahan jika ada. Setelah selesai, tagihan DP akan otomatis muncul di tab Tagihan & Pembayaran dan Anda dapat melakukan pembayaran.</p>';
                wrap.appendChild(dpPendingInfoDiv);

                if (waPhone && waTemplates && waTemplates.support) {
                    var waBtnDp = document.createElement("a");
                    waBtnDp.className = "cta cta-outline mb-2";
                    waBtnDp.href = buildWhatsappUrl(waPhone, waTemplates.support);
                    waBtnDp.target = "_blank";
                    waBtnDp.rel = "noopener";
                    waBtnDp.innerHTML = '<i class="ri-whatsapp-line me-1"></i> Tanyakan Detail via WhatsApp';
                    wrap.appendChild(waBtnDp);
                }
            }

            if (isApprovedWaitingFinal && !hasFinalInstallment) {
                var finalPendingInfoDiv = document.createElement("div");
                finalPendingInfoDiv.className = "estimate-box mb-3";
                finalPendingInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-search-line me-1"></i><strong>Menunggu Tagihan Pelunasan</strong></p><p class="estimate-note mb-0">Pembayaran DP Anda telah <strong>diverifikasi</strong>. Tim Etherno sedang menyiapkan tagihan pelunasan Anda. Setelah selesai, tagihan pelunasan akan otomatis muncul di tab Tagihan & Pembayaran.</p>';
                wrap.appendChild(finalPendingInfoDiv);

                if (waPhone && waTemplates && waTemplates.support) {
                    var waBtnFinal = document.createElement("a");
                    waBtnFinal.className = "cta cta-outline mb-2";
                    waBtnFinal.href = buildWhatsappUrl(waPhone, waTemplates.support);
                    waBtnFinal.target = "_blank";
                    waBtnFinal.rel = "noopener";
                    waBtnFinal.innerHTML = '<i class="ri-whatsapp-line me-1"></i> Tanyakan Detail via WhatsApp';
                    wrap.appendChild(waBtnFinal);
                }
            }

            if (isApprovedWaitingFinal && hasFinalInstallment) {
                var finalInst = billing.installments.find(function (i) {
                    return String(i.type_code || "") === "INS_FINAL";
                });
                var finalPending = finalInst && finalInst.has_pending_payment;
                var finalReadyInfoDiv = document.createElement("div");
                finalReadyInfoDiv.className = "estimate-box mb-3";
                if (finalPending) {
                    finalReadyInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-time-line me-1"></i><strong>Pelunasan Sedang Diverifikasi</strong></p><p class="estimate-note mb-0">Terima kasih! Bukti pembayaran pelunasan Anda sudah kami terima dan sedang dalam proses verifikasi. Anda akan dihubungi melalui WhatsApp setelah pembayaran terverifikasi.</p>';
                } else {
                    finalReadyInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-check-double-line me-1"></i><strong>DP Diverifikasi — Siap Pelunasan</strong></p><p class="estimate-note mb-0">Pembayaran DP Anda telah diverifikasi. Tagihan pelunasan sudah tersedia di tab Tagihan & Pembayaran. Silakan lakukan pembayaran pelunasan sebelum tanggal acara.</p>';
                }
                wrap.appendChild(finalReadyInfoDiv);
            }

            var latestRejectedRescheduleReason = findLatestHistoryDescription("reschedule", "ditolak");
            if (latestRejectedRescheduleReason) {
                var rejectedRescheduleInfoDiv = document.createElement("div");
                rejectedRescheduleInfoDiv.className = "estimate-box mb-3";
                rejectedRescheduleInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-calendar-close-line me-1"></i><strong>Reschedule Ditolak</strong></p><p class="estimate-note mb-0"></p>';
                rejectedRescheduleInfoDiv.querySelector(".estimate-note.mb-0").textContent = latestRejectedRescheduleReason;
                wrap.appendChild(rejectedRescheduleInfoDiv);
            }

            if (isConfirmed) {
                var confirmedInfoDiv = document.createElement("div");
                confirmedInfoDiv.className = "estimate-box mb-3";
                confirmedInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-party-line me-1"></i><strong>Booking Dikunci!</strong></p><p class="estimate-note mb-0">Selamat! Semua pembayaran telah lunas dan terverifikasi. Slot jadwal Anda sudah dikunci. Tim Etherno akan menghubungi Anda untuk koordinasi sebelum hari acara. Sampai jumpa!</p>';
                wrap.appendChild(confirmedInfoDiv);

                if (waPhone && waTemplates && waTemplates.support) {
                    var waBtnConf = document.createElement("a");
                    waBtnConf.className = "cta cta-outline mb-2";
                    waBtnConf.href = buildWhatsappUrl(waPhone, waTemplates.support);
                    waBtnConf.target = "_blank";
                    waBtnConf.rel = "noopener";
                    waBtnConf.innerHTML = '<i class="ri-whatsapp-line me-1"></i> Koordinasi via WhatsApp';
                    wrap.appendChild(waBtnConf);
                }
            }

            if (isReschedule) {
                var rescheduleInfoDiv = document.createElement("div");
                rescheduleInfoDiv.className = "estimate-box mb-3";
                rescheduleInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-time-line me-1"></i><strong>Reschedule Menunggu Review</strong></p><p class="estimate-note mb-0">Request reschedule Anda sudah masuk. Tim Etherno sedang mengecek ketersediaan jadwal dan akan menghubungi Anda melalui WhatsApp.</p>';
                wrap.appendChild(rescheduleInfoDiv);
            }

            if (isForceMajeure) {
                var forceMajeureData = currentPayload && currentPayload.force_majeure ? currentPayload.force_majeure : {};
                var fmReason = String(forceMajeureData.reason || "").trim();
                var fmType = String(forceMajeureData.type || "").trim();
                var fmDateLabel = String(forceMajeureData.date_label || "").trim();
                var fmTitle = fmType === "reschedule" ? "Force Majeure - Usulan Reschedule" : "Force Majeure - Usulan Refund";
                var fmCopy = fmType === "reschedule"
                    ? "Tim Etherno mengajukan perubahan jadwal karena kondisi force majeure. Usulan tanggal baru: " + (fmDateLabel || "-") + "."
                    : "Tim Etherno mengajukan proses refund karena kondisi force majeure pada jadwal Anda.";
                if (fmReason) {
                    fmCopy += " Alasan: " + fmReason;
                }

                var forceMajeureInfoDiv = document.createElement("div");
                forceMajeureInfoDiv.className = "estimate-box mb-3";
                forceMajeureInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-alert-line me-1"></i><strong></strong></p><p class="estimate-note mb-0"></p>';
                forceMajeureInfoDiv.querySelector("strong").textContent = fmTitle;
                forceMajeureInfoDiv.querySelector(".estimate-note.mb-0").textContent = fmCopy;
                wrap.appendChild(forceMajeureInfoDiv);

                if (waPhone && waTemplates && waTemplates.force_majeure) {
                    var waBtnFm = document.createElement("a");
                    waBtnFm.className = "cta cta-outline mb-2";
                    waBtnFm.href = buildWhatsappUrl(waPhone, waTemplates.force_majeure);
                    waBtnFm.target = "_blank";
                    waBtnFm.rel = "noopener";
                    waBtnFm.innerHTML = '<i class="ri-whatsapp-line me-1"></i> Diskusikan Force Majeure via WhatsApp';
                    wrap.appendChild(waBtnFm);
                }
            }

            if (isRefund) {
                var refundInfoDiv = document.createElement("div");
                refundInfoDiv.className = "estimate-box mb-3";
                refundInfoDiv.innerHTML = '<p class="estimate-note mb-1"><i class="ri-refund-2-line me-1"></i><strong>Refund Sedang / Sudah Diproses</strong></p><p class="estimate-note mb-0">Booking Anda masuk ke tahap refund. Jika bukti refund sudah diproses oleh tim Etherno, dana akan dikembalikan sesuai nominal refund yang telah disetujui. Untuk memastikan progres terakhir, silakan hubungi tim kami melalui WhatsApp.</p>';
                wrap.appendChild(refundInfoDiv);

                if (waPhone && waTemplates && waTemplates.support) {
                    var waBtnRefund = document.createElement("a");
                    waBtnRefund.className = "cta cta-outline mb-2";
                    waBtnRefund.href = buildWhatsappUrl(waPhone, waTemplates.support);
                    waBtnRefund.target = "_blank";
                    waBtnRefund.rel = "noopener";
                    waBtnRefund.innerHTML = '<i class="ri-whatsapp-line me-1"></i> Konfirmasi Refund via WhatsApp';
                    wrap.appendChild(waBtnRefund);
                }
            }

            if (!Array.isArray(actions) || actions.length === 0) {
                if (!isWaitingApproval && !(isApprovedWaitingDp && !hasDpInstallment) && !(isApprovedWaitingFinal && !hasFinalInstallment) && !isConfirmed && !isReschedule && !isForceMajeure && !isRefund) {
                    var empty = document.createElement("p");
                    empty.className = "booking-disclaimer";
                    empty.textContent = "Tidak ada aksi yang tersedia saat ini.";
                    wrap.appendChild(empty);
                }
                return;
            }

            actions.forEach(function (action) {
                if (action === "upload_dp" || action === "upload_dp_pending") {
                    var dpInst = findPayableDpInstallment(billing);
                    if (action === "upload_dp_pending" || (dpInst && dpInst.has_pending_payment)) {
                        renderPendingInfo(wrap, "DP", waPhone, waTemplates.dp_paid);
                    } else if (dpInst) {
                        var btn = document.createElement("button");
                        btn.className = "cta mb-2";
                        btn.innerHTML = '<i class="ri-upload-line me-1"></i> Upload Bukti DP';
                        btn.setAttribute("data-installment-id", dpInst.id);
                        btn.setAttribute("data-installment-remaining", dpInst.remaining_label);
                        btn.addEventListener("click", function () {
                            openUploadPaymentModal(dpInst.id, "DP", dpInst.remaining_label, dpInst.amount_label);
                        });
                        wrap.appendChild(btn);
                    }
                }
                if (action === "upload_final" || action === "upload_final_pending") {
                    var finalInsts = findPayableNonDpInstallments(billing);
                    finalInsts.forEach(function (fi) {
                        if (action === "upload_final_pending" || fi.has_pending_payment) {
                            renderPendingInfo(wrap, fi.type, waPhone, waTemplates.final_paid);
                        } else {
                            var btn = document.createElement("button");
                            btn.className = "cta cta-outline mb-2";
                            btn.innerHTML = '<i class="ri-upload-line me-1"></i> Upload Bukti ' + String(fi.type || "Pembayaran") + " (" + String(fi.remaining_label || "-") + ")";
                            btn.addEventListener("click", function () {
                                openUploadPaymentModal(fi.id, fi.type, fi.remaining_label, fi.amount_label);
                            });
                            wrap.appendChild(btn);
                        }
                    });
                }
                if (action === "reschedule_request") {
                    var maxDaysInfo = 14;
                    var eventDateRawInfo = "";
                    if (currentPayload && currentPayload.event && currentPayload.event.date_raw) {
                        eventDateRawInfo = String(currentPayload.event.date_raw).trim();
                    }
                    var infoDeadlineText = "";
                    if (eventDateRawInfo) {
                        var evDate = new Date(eventDateRawInfo + "T00:00:00");
                        if (!isNaN(evDate.getTime())) {
                            var dlDate = new Date(evDate);
                            dlDate.setDate(dlDate.getDate() - maxDaysInfo);
                            var dlStr = dlDate.toLocaleDateString("id-ID", {
                                weekday: "long", day: "numeric", month: "long", year: "numeric"
                            });
                            infoDeadlineText = ' Batas pengajuan: <strong>' + dlStr + '</strong> (H-' + maxDaysInfo + ').';
                        }
                    }

                    var info = document.createElement("div");
                    info.className = "estimate-box mb-2";
                    info.innerHTML = '<p class="estimate-note mb-1"><i class="ri-calendar-event-line me-1"></i><strong>Ajukan Reschedule</strong></p><p class="estimate-note mb-0">Anda dapat mengajukan perubahan tanggal acara. Tim Etherno akan mengecek ketersediaan jadwal sebelum menyetujui perubahan.' + infoDeadlineText + '</p>';
                    wrap.appendChild(info);

                    var rescheduleBtn = document.createElement("button");
                    rescheduleBtn.className = "cta cta-outline mb-2";
                    rescheduleBtn.type = "button";
                    rescheduleBtn.innerHTML = '<i class="ri-calendar-check-line me-1"></i> Ajukan Reschedule';
                    rescheduleBtn.addEventListener("click", openRescheduleModal);
                    wrap.appendChild(rescheduleBtn);
                }
            });
        }

        function openRescheduleModal() {
            if (!rescheduleModal) return;
            if (rescheduleError) rescheduleError.hidden = true;
            if (rescheduleSuccess) rescheduleSuccess.style.display = "none";

            var maxDays = 14;
            var eventDateRaw = "";
            if (currentPayload && currentPayload.event && currentPayload.event.date_raw) {
                eventDateRaw = String(currentPayload.event.date_raw).trim();
            }

            var deadlineInfo = document.getElementById("reschedule_deadline_info");
            var dateInput = document.getElementById("reschedule_request_date");

            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var todayStr = today.getFullYear() + "-" +
                String(today.getMonth() + 1).padStart(2, "0") + "-" +
                String(today.getDate()).padStart(2, "0");

            if (dateInput) {
                dateInput.min = todayStr;
            }

            if (eventDateRaw && deadlineInfo) {
                var eventDate = new Date(eventDateRaw + "T00:00:00");
                if (!isNaN(eventDate.getTime())) {
                    var deadlineDate = new Date(eventDate);
                    deadlineDate.setDate(deadlineDate.getDate() - maxDays);

                    var isPastDeadline = today > deadlineDate;

                    var deadlineStr = deadlineDate.toLocaleDateString("id-ID", {
                        weekday: "long", day: "numeric", month: "long", year: "numeric"
                    });

                    var noteEl = deadlineInfo.querySelector(".estimate-note");
                    if (noteEl) {
                        if (isPastDeadline) {
                            noteEl.innerHTML = '<i class="ri-error-warning-line me-1"></i><strong>Batas Reschedule Terlewati:</strong> Pengajuan reschedule seharusnya diajukan paling lambat <strong>' + deadlineStr + '</strong> (H-' + maxDays + ' sebelum acara). Hubungi tim Etherno via WhatsApp untuk bantuan.';
                        } else {
                            noteEl.innerHTML = '<i class="ri-calendar-event-line me-1"></i><strong>Batas Pengajuan:</strong> Reschedule harus diajukan paling lambat <strong>' + deadlineStr + '</strong> (H-' + maxDays + ' sebelum acara). Pilih tanggal baru di luar tanggal masa lalu.';
                        }
                    }
                    deadlineInfo.hidden = false;
                }
            } else if (deadlineInfo) {
                deadlineInfo.hidden = true;
            }

            rescheduleModal.hidden = false;
            document.body.classList.add("booking-confirm-open");
            if (dateInput) dateInput.focus();
        }

        function closeRescheduleModal() {
            if (!rescheduleModal) return;
            rescheduleModal.hidden = true;
            document.body.classList.remove("booking-confirm-open");
        }

        function submitRescheduleRequest(event) {
            event.preventDefault();
            if (!rescheduleForm || !currentPayload) return;
            var proposedDateInput = document.getElementById("reschedule_request_date");
            var reasonInput = document.getElementById("reschedule_request_reason");
            var proposedDate = proposedDateInput ? String(proposedDateInput.value || "").trim() : "";
            var reason = reasonInput ? String(reasonInput.value || "").trim() : "";

            if (rescheduleError) rescheduleError.hidden = true;
            if (rescheduleSuccess) rescheduleSuccess.style.display = "none";

            if (!proposedDate || !reason) {
                if (rescheduleError) {
                    rescheduleError.textContent = "Tanggal baru dan alasan reschedule wajib diisi.";
                    rescheduleError.hidden = false;
                }
                return;
            }

            if (rescheduleSubmitBtn) {
                rescheduleSubmitBtn.disabled = true;
                rescheduleSubmitBtn.textContent = "Mengirim...";
            }

            fetch("/api/booking/reschedule-request", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({
                    booking_code: String(bookingCodeInput.value || "").trim(),
                    phone_last4: sanitizeLastFour(verifyInput ? verifyInput.value : ""),
                    proposed_date: proposedDate,
                    reason: reason
                })
            })
            .then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (data) {
                    if (!response.ok) throw new Error(data.message || "Gagal mengirim request reschedule.");
                    return data;
                });
            })
            .then(function (data) {
                if (rescheduleSuccess) {
                    rescheduleSuccess.textContent = data.message || "Request reschedule berhasil dikirim.";
                    rescheduleSuccess.style.display = "block";
                }
                rescheduleForm.reset();
                return submitLookup().then(function () {
                    window.setTimeout(closeRescheduleModal, 600);
                });
            })
            .catch(function (error) {
                if (rescheduleError) {
                    rescheduleError.textContent = error.message || "Terjadi kesalahan. Silakan coba lagi.";
                    rescheduleError.hidden = false;
                }
            })
            .finally(function () {
                if (rescheduleSubmitBtn) {
                    rescheduleSubmitBtn.disabled = false;
                    rescheduleSubmitBtn.textContent = "Kirim Request Reschedule";
                }
            });
        }

        function renderPendingInfo(wrap, type, phone, template) {
            var box = document.createElement("div");
            box.className = "estimate-box mb-2";
            box.innerHTML = '<p class="estimate-note mb-1">\u23F3 Bukti pembayaran ' + String(type || '') + ' sudah dikirim dan sedang menunggu verifikasi dari tim kami.</p><p class="estimate-note mb-0" style="font-size:0.85em;">Proses verifikasi biasanya 1x24 jam. Jika bukti ditolak, Anda dapat mengirim ulang dari halaman ini.</p>';
            wrap.appendChild(box);
            if (phone && template) {
                var waLink = document.createElement("a");
                waLink.className = "cta cta-outline mb-2";
                waLink.href = buildWhatsappUrl(phone, template);
                waLink.target = "_blank";
                waLink.rel = "noopener";
                waLink.innerHTML = '<i class="ri-whatsapp-line me-1"></i> Konfirmasi via WhatsApp';
                wrap.appendChild(waLink);
            }
        }

        function findPayableDpInstallment(billing) {
            if (!billing || !Array.isArray(billing.installments)) return null;
            return billing.installments.find(function (i) {
                if (i.type_code !== "INS_DP") return false;
                if ((i.remaining_amount || 0) > 0) return true;
                if (Array.isArray(i.payments)) {
                    var hasFailed = i.payments.some(function (p) { return String(p.status_code || "") === "PYS_FAILED"; });
                    if (hasFailed) return true;
                }
                return false;
            }) || null;
        }

        function findPayableNonDpInstallments(billing) {
            if (!billing || !Array.isArray(billing.installments)) return [];
            return billing.installments.filter(function (i) {
                if (i.type_code === "INS_DP" || i.type_code === "INS_REFUND") return false;
                if ((i.remaining_amount || 0) > 0) return true;
                if (Array.isArray(i.payments)) {
                    var hasFailed = i.payments.some(function (p) { return String(p.status_code || "") === "PYS_FAILED"; });
                    if (hasFailed) return true;
                }
                return false;
            });
        }

        function openUploadPaymentModal(installmentId, type, remainingLabel, amountLabel) {
            if (!uploadPaymentModal) return;
            uploadPaymentInstallmentId.value = String(installmentId);
            uploadPaymentAmountInfo.textContent = "Tipe: " + String(type || "-") + " | Nominal Tagihan: " + String(amountLabel || "-") + " | Sisa: " + String(remainingLabel || "-");
            uploadPaymentError.hidden = true;
            uploadPaymentSubmitBtn.disabled = false;
            uploadPaymentSubmitBtn.textContent = "Kirim Bukti Pembayaran";
            var fileInput = document.getElementById("upload_payment_receipt");
            if (fileInput) fileInput.value = "";
            uploadPaymentModal.hidden = false;
            document.body.classList.add("booking-confirm-open");
        }

        function closeUploadPaymentModal() {
            if (!uploadPaymentModal) return;
            uploadPaymentModal.hidden = true;
            document.body.classList.remove("booking-confirm-open");
        }

        function submitPaymentProof() {
            if (isSubmitting || !uploadPaymentForm || !currentPayload) return;
            uploadPaymentError.hidden = true;

            var fileInput = document.getElementById("upload_payment_receipt");
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                uploadPaymentError.textContent = "Pilih file bukti transfer terlebih dahulu.";
                uploadPaymentError.hidden = false;
                return;
            }

            var formData = new FormData();
            formData.append("booking_code", String(bookingCodeInput.value || "").trim());
            formData.append("phone_last4", sanitizeLastFour(verifyInput ? verifyInput.value : ""));
            formData.append("billing_installment_id", String(uploadPaymentInstallmentId.value || ""));
            if (fileInput.files && fileInput.files.length > 0) {
                formData.append("transfer_receipt", fileInput.files[0]);
            }

            isSubmitting = true;
            uploadPaymentSubmitBtn.disabled = true;
            uploadPaymentSubmitBtn.textContent = "Mengirim...";

            fetch(uploadPaymentUrl, {
                method: "POST",
                credentials: "same-origin",
                body: formData,
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (data) {
                    if (!response.ok) {
                        var msg = data.message || "Gagal mengirim bukti pembayaran.";
                        if (data.errors) {
                            var fieldErrors = [];
                            for (var field in data.errors) {
                                if (data.errors.hasOwnProperty(field)) {
                                    fieldErrors = fieldErrors.concat(data.errors[field]);
                                }
                            }
                            if (fieldErrors.length > 0) msg = fieldErrors.join(" ");
                        }
                        throw new Error(msg);
                    }
                    return data;
                });
            })
            .then(function (data) {
                closeUploadPaymentModal();
                isSubmitting = false;

                var refreshBtn = document.getElementById("btn_refresh_status");
                if (refreshBtn) {
                    refreshBtn.disabled = true;
                    refreshBtn.textContent = "Memuat ulang data...";
                }

                submitLookup().then(function () {
                    if (refreshBtn) {
                        refreshBtn.disabled = false;
                        refreshBtn.textContent = "Refresh Data Booking";
                    }
                }).catch(function () {
                    if (refreshBtn) {
                        refreshBtn.disabled = false;
                        refreshBtn.textContent = "Refresh Data Booking";
                    }
                });
            })
            .catch(function (err) {
                var msg = err instanceof Error ? err.message : "Terjadi kendala saat mengirim bukti pembayaran.";
                uploadPaymentError.textContent = msg;
                uploadPaymentError.hidden = false;
            })
            .finally(function () {
                isSubmitting = false;
                uploadPaymentSubmitBtn.disabled = false;
                uploadPaymentSubmitBtn.textContent = "Kirim Bukti Pembayaran";
            });
        }

        function renderPayload(payload) {
            currentPayload = payload;
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
            if (statusStateLabel) statusStateLabel.textContent = "Status: " + String(status.label || "-");
            var code = String(status.code || "");
            var subtitleText = "Diajukan pada " + String(eventData.submitted_at || "-");

            if (code === "BS_WAITING_APPROVAL") {
                subtitleText += ". Pengajuan Anda sedang diverifikasi oleh tim kami. Mohon tunggu konfirmasi dari admin.";
            }

            if (code === "BS_APPROVED_WAITING_DP") {
                var dpInstallment = billing && Array.isArray(billing.installments) ? billing.installments.find(function (i) {
                    return String(i.type_code || "") === "INS_DP";
                }) : null;
                if (dpInstallment && dpInstallment.has_pending_payment) {
                    subtitleText += ". Bukti pembayaran DP sudah dikirim dan sedang menunggu verifikasi dari tim kami.";
                } else if (dpInstallment) {
                    subtitleText += ". Tagihan DP sudah tersedia. Silakan lakukan pembayaran DP sesuai nominal yang tertera.";
                } else {
                    subtitleText += ". Pengajuan Anda telah disetujui. Tim kami sedang menyiapkan tagihan DP Anda.";
                }
            }

            if (code === "BS_APPROVED_WAITING_FINAL_PAYMENT") {
                var finalInstallment = billing && Array.isArray(billing.installments) ? billing.installments.find(function (i) {
                    return String(i.type_code || "") === "INS_FINAL";
                }) : null;
                if (finalInstallment && finalInstallment.has_pending_payment) {
                    subtitleText += ". Bukti pembayaran pelunasan sudah dikirim dan sedang menunggu verifikasi dari tim kami.";
                } else if (finalInstallment) {
                    subtitleText += ". Tagihan pelunasan sudah tersedia. Silakan lakukan pembayaran pelunasan sebelum tanggal acara.";
                } else {
                    subtitleText += ". DP Anda telah diverifikasi. Tim kami sedang menyiapkan tagihan pelunasan Anda.";
                }
            }

            if (code === "BS_CONFIRMED") {
                subtitleText += ". Pembayaran Anda telah lunas dan terverifikasi. Booking Anda sudah dikunci. Sampai jumpa di hari acara!";
            }

            if (code === "BS_RESCHEDULE") {
                subtitleText += ". Request reschedule Anda sedang menunggu review tim Etherno.";
            }

            if (code === "BS_FORCE_MAJEURE") {
                var forceMajeureData = payload.force_majeure || {};
                var fmReason = String(forceMajeureData.reason || "").trim();
                var fmType = String(forceMajeureData.type || "").trim();
                var fmDateLabel = String(forceMajeureData.date_label || "").trim();
                if (fmType === "reschedule") {
                    subtitleText += ". Tim Etherno mengajukan force majeure dengan usulan reschedule ke " + (fmDateLabel || "tanggal baru") + ".";
                } else {
                    subtitleText += ". Tim Etherno mengajukan force majeure dengan opsi refund untuk booking Anda.";
                }
                if (fmReason) {
                    subtitleText += " Alasan: " + fmReason;
                }
            }

            if (code === "BS_COMPLETE") {
                subtitleText += ". Acara telah selesai. Terima kasih telah mempercayakan moment spesial Anda kepada Etherno.";
            }

            if (code === "BS_REFUND") {
                subtitleText += ". Booking Anda berada pada tahap refund. Silakan cek tab Tagihan & Pembayaran atau hubungi tim Etherno melalui WhatsApp untuk memastikan progres pengembalian dana.";
            }
            if (statusStateSubtitle) statusStateSubtitle.textContent = subtitleText;

            setText("status_case_id", payload.booking_case_id);
            setText("status_request_code", payload.request_code);
            setText("status_customer_name", customer.name);
            setText("status_customer_phone", customer.phone_masked);
            setText("status_event_date", eventData.date_label);
            setText("status_event_session", eventData.session);
            setText("status_package_name", packageData.name);
            setText("status_package_type", packageData.type);
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
                setText("status_billing_status", "Status: " + String(billing.status || "-"));
                setText("status_billing_total", String(billing.total || "-"));
                setText("status_billing_paid", String(billing.paid || "-"));
                setText("status_billing_remaining", String(billing.remaining || "-"));
                renderBillingDetails(billing.details || []);
                renderInstallments(billing.installments || []);
            } else {
                setText("status_billing_status", "Belum ada data pembayaran.");
                setText("status_billing_total", "-");
                setText("status_billing_paid", "-");
                setText("status_billing_remaining", "-");
                renderBillingDetails([]);
                renderInstallments([]);
            }

            renderHistory(payload.history || []);
            var adminWa = String(payload.admin_whatsapp || "").trim();
            var waTemplates = payload.whatsapp_templates || {};
            renderCustomerActions(payload.customer_actions || [], billing, adminWa, waTemplates);

            var waSupportLink = document.getElementById("btn_wa_support");
            if (waSupportLink) {
                if (adminWa !== "") {
                    waSupportLink.href = buildWhatsappUrl(adminWa, waTemplates.support || "");
                    waSupportLink.hidden = false;
                } else {
                    waSupportLink.href = "#";
                    waSupportLink.hidden = true;
                }
            }

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

        function submitLookup() {
            if (!bookingCodeInput || !verifyInput) return Promise.resolve();
            if (isSubmitting) return Promise.resolve();
            var bookingCode = String(bookingCodeInput.value || "").trim();
            var phoneLast4 = sanitizeLastFour(verifyInput.value);
            setVerifyError("");
            setLookupError("");

            if (phoneLast4.length !== 4) {
                setVerifyError("Masukkan tepat 4 digit terakhir nomor WhatsApp.");
                verifyInput.focus();
                return Promise.resolve();
            }
            if (lookupUrl === "") {
                setVerifyError("Endpoint cek status belum tersedia.");
                return Promise.resolve();
            }

            isSubmitting = true;
            verifySubmitButton.disabled = true;
            verifySubmitButton.textContent = "Memverifikasi...";

            var requestUrl = new URL(lookupUrl, window.location.origin);
            requestUrl.searchParams.set("booking_code", bookingCode);
            requestUrl.searchParams.set("phone_last4", phoneLast4);

            return fetch(requestUrl.toString(), {
                method: "GET",
                credentials: "same-origin",
                headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
            })
            .then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (payload) {
                    if (!response.ok) throw new Error(String(payload.message || "Data booking tidak ditemukan atau verifikasi tidak sesuai."));
                    return payload;
                });
            })
            .then(function (payload) {
                closeVerifyModal();
                try {
                    renderPayload(payload);
                } catch (error) {
                    console.error("Failed to render booking payload", payload, error);
                    throw new Error("Data booking berhasil ditemukan, tetapi tampilan detail gagal dirender. Silakan refresh halaman lalu coba lagi.");
                }
            })
            .catch(function (error) {
                var message = error instanceof Error ? error.message : "Terjadi kendala saat mengambil data booking.";
                setVerifyError(message);
                setLookupError(message);
            })
            .finally(function () {
                isSubmitting = false;
                verifySubmitButton.disabled = false;
                verifySubmitButton.textContent = "Verifikasi & Tampilkan";
            });
        }

        var uploadPaymentUrl = String(form.getAttribute("data-upload-payment-url") || "").trim();

        if (bookingCodeInput) {
            bookingCodeInput.addEventListener("input", function () { setLookupError(""); });
        }

        if (verifyInput) {
            verifyInput.addEventListener("input", function () {
                verifyInput.value = sanitizeLastFour(verifyInput.value);
                setVerifyError("");
            });
            verifyInput.addEventListener("keydown", function (e) {
                if (e.key === "Enter") { e.preventDefault(); submitLookup(); }
            });
        }

        modalCloseButtons.forEach(function (btn) {
            btn.addEventListener("click", closeVerifyModal);
        });

        uploadPaymentCloseButtons.forEach(function (btn) {
            btn.addEventListener("click", closeUploadPaymentModal);
        });

        rescheduleCloseButtons.forEach(function (btn) {
            btn.addEventListener("click", closeRescheduleModal);
        });

        if (openVerifyButton) {
            openVerifyButton.addEventListener("click", function (e) { e.preventDefault(); openVerifyModal(); });
        }

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            if (typeof form.reportValidity === "function" && !form.reportValidity()) return;
            openVerifyModal();
        });

        if (verifySubmitButton) {
            verifySubmitButton.addEventListener("click", submitLookup);
        }

        if (uploadPaymentForm) {
            uploadPaymentForm.addEventListener("submit", function (e) {
                e.preventDefault();
                submitPaymentProof();
            });
        }

        if (rescheduleForm) {
            rescheduleForm.addEventListener("submit", submitRescheduleRequest);
        }

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                if (modal && !modal.hidden) closeVerifyModal();
                if (uploadPaymentModal && !uploadPaymentModal.hidden) closeUploadPaymentModal();
                if (rescheduleModal && !rescheduleModal.hidden) closeRescheduleModal();
            }
        });

        var statusTabs = document.querySelector("[data-status-tabs]");
        if (statusTabs) {
            var tabBtns = statusTabs.querySelectorAll("[data-status-tab]");
            var tabPanels = statusTabs.querySelectorAll("[data-status-panel]");

            function setActiveStatusTab(tabName) {
                tabBtns.forEach(function (btn) {
                    var isActive = btn.getAttribute("data-status-tab") === tabName;
                    btn.classList.toggle("is-active", isActive);
                });
                tabPanels.forEach(function (panel) {
                    var isActive = panel.getAttribute("data-status-panel") === tabName;
                    panel.classList.toggle("is-active", isActive);
                    panel.hidden = !isActive;
                });
            }

            tabBtns.forEach(function (btn) {
                btn.addEventListener("click", function () {
                    setActiveStatusTab(btn.getAttribute("data-status-tab"));
                });
            });

            setActiveStatusTab("info");
        }

        var billingTabs = document.querySelector("[data-billing-tabs]");
        if (billingTabs) {
            var billingTabBtns = billingTabs.querySelectorAll("[data-billing-tab]");
            var billingTabPanels = billingTabs.querySelectorAll("[data-billing-panel]");

            function setActiveBillingTab(tabName) {
                billingTabBtns.forEach(function (btn) {
                    var isActive = btn.getAttribute("data-billing-tab") === tabName;
                    btn.classList.toggle("is-active", isActive);
                });
                billingTabPanels.forEach(function (panel) {
                    var isActive = panel.getAttribute("data-billing-panel") === tabName;
                    panel.classList.toggle("is-active", isActive);
                    panel.hidden = !isActive;
                });
            }

            billingTabBtns.forEach(function (btn) {
                btn.addEventListener("click", function () {
                    setActiveBillingTab(btn.getAttribute("data-billing-tab"));
                });
            });

            setActiveBillingTab("details");
        }

        var refreshBtn = document.getElementById("btn_refresh_status");
        if (refreshBtn) {
            refreshBtn.addEventListener("click", function () {
                if (currentPayload) {
                    refreshBtn.disabled = true;
                    refreshBtn.textContent = "Memuat ulang...";
                    submitLookup().finally(function () {
                        refreshBtn.disabled = false;
                        refreshBtn.textContent = "Refresh Data Booking";
                    });
                } else {
                    alert("Data booking belum dimuat. Cari booking terlebih dahulu.");
                }
            });
        }
    });
})();
