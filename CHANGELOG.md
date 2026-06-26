# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [v1.5.5] - 2026-06-22

### Fixed
- Perbaikan kalender booking: rentang selectable hari diubah dari H+7 menjadi H-0 (hari ini) terhadap hari yang dipilih.
- Perbaikan copywriting status "pelunasan payed" pada halaman booking.

## [v1.5.4] - 2026-06-21

### Fixed
- Perbaikan flow reschedule dan force majeure reschedule.

## [v1.5.3] - 2026-06-21

### Added
- Fitur smooth scroll to top.
- Tombol scrollable up pada landing page.

### Fixed
- Perbaikan responsive mobile pada landing page (public).
- Perbaikan image yang hilang pada landing page.
- Perbaikan style booking pada landing page.
- Perbaikan gambar paket yang tidak tampil.
- Perbaikan konflik scroll antara nav dan button.
- Perbaikan wording status DP.
- Perbaikan bug umum lainnya.

## [v1.5.2] - 2026-06-21

### Added
- Menu app setting dan migration WhatsApp setting.

### Changed
- Mengganti traditional alert menjadi SweetAlert (SWAL).

### Fixed
- Perbaikan error pada generated bill.
- Perbaikan copywriting status booking.
- Menghapus field alamat paket pada halaman admin.
- Perbaikan redirect setelah create account admin.

## [v1.5.1] - 2026-06-20

### Fixed
- Perbaikan library yang missing.

## [v1.5.0] - 2026-06-20

### Added
- Validasi HTTPS pada route.

## [v1.4.0] - 2026-06-20

### Added
- Dockerize project untuk deployment ke VPS.

## [v1.3.0] - 2026-06-20

### Added
- Section photo slider pada landing page.
- Fitur chat to admin.
- Informasi "DP telah dibayarkan" pada customer di aksi dan status cek booking.

### Changed
- Flow DP diubah dari auto-generate (saat approve status) menjadi manual installment DP.
- Desain FAQ diubah dari card menjadi list dropdown.
- Migration alter reference untuk mengganti `price_type` name.

### Fixed
- Fix all feature dan baseline first version.
- Menghapus sub title untuk product wedding dan non-wedding.
- Perbaikan informasi kecil pada top side website admin.
- Menghapus alamat paket pada booking form.
- Perbaikan route approve untuk petugas.
- Perbaikan bug lainnya pada booking petugas.

## [v1.1.14] - 2026-05-29

### Added
- Function flow booking untuk sisi public.

## [v1.1.13] - 2026-05-29

### Fixed
- Perbaikan flow form booking dan status booking.

## [v1.1.12] - 2026-05-28

### Added
- Fetch data packages dan inisialisasi booking flow.
- Logika form booking dan list daftar booking.

### Fixed
- Revisi form booking dan list daftar booking.

## [v1.1.11] - 2026-05-28

### Added
- CRUD user management dan profile.
- CRUD location price rule.
- CRUD packages dan packages benefit.
- CRUD settings payment date rule.
- CRUD payment DP percentage.
- CRUD settings package date rule.
- Input photo profile pada CRUD user dan edit profile.

### Changed
- Revisi controller web dan api.

### Fixed
- Perbaikan CRUD location rules pricing.
- Revisi attachment temp.

## [v1.1.10] - 2026-05-28

### Added
- Revisi base scaffolding: penambahan section API dan web untuk controller dan route.
- Error pages.
- Completion auth.

### Changed
- Revisi migration.

## [v1.1.9] - 2026-05-27

### Added
- Base scaffolding menu access dan routing.

### Changed
- Revisi README.md untuk business dan tech knowledge.

### Fixed
- Perbaikan typo pada booking table dan model.

## [v1.1.8] - 2026-05-27

### Added
- Migration billing, billing details, billing installments, dan payment.
- Model untuk setiap table.
- Model repository dan repository interface.

### Changed
- Penambahan indexing ke tabel untuk performa.

## [v1.1.6] - 2026-05-27

### Added
- Migration roles, alter user, customer, booking, dan booking history.

## [v1.1.5] - 2026-05-27

### Added
- Migration attachment, packages, package benefit, dan setting.

## [v1.1.4] - 2026-05-27

### Added
- Layout responsive public.
- Form booking.
- Public views.
- Admin menu.
- Logo white dan perubahan intim package.

## [v1.1.3] - 2026-05-26

### Added
- Base wilayah dari dataset ke dalam migration.
- Login page dan auth function.
- Migration insert reference dan transform wilayah to location.
- Migration location rules dan reference price rule.

### Fixed
- Perbaikan migration base wilayah, penambahan create reference dan location.

## [v1.1.1] - 2026-05-19

### Added
- Banner pada landing page.

### Fixed
- Perbaikan bahasa dari English ke Indonesia.
- Perbaikan icon pada footer.

## [v1.1.0] - 2026-05-19

### Added
- Asset template Nowa.
- Konversi template Nowa menjadi admin layout.
- Layout public.
- Temporary disable portofolio.

### Fixed
- Perbaikan asset link.
- Revisi template public.

## [v1.0.0] - 2026-05-18

### Added
- Inisialisasi project (init project).
- Versioning pada migration.
