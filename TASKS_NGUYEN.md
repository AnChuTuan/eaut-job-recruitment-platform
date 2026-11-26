# 📋 Kế Hoạch & Tiến Độ Nhiệm Vụ - Thành Viên: NGUYÊN (Developer / QA)
> **Dự án:** EAUT Job Recruitment Platform (Nền tảng tuyển dụng sinh viên & doanh nghiệp - ĐH Công nghệ Đông Á)  
> **Nhánh Git:** `feature/nguyen`  
> **Trạng thái:** ✅ **Đã hoàn thiện & Test Pass 100% (39/39 Tests, 114 Assertions)**

---

## 📌 I. Sơ đồ Use Case: Manage User Accounts (Quản lý tài khoản người dùng)

Module được xây dựng tuân thủ theo tài liệu phân tích hệ thống (hiển thị trực tiếp thông tin liên hệ rõ ràng, không che):
1. **Main Flow:** Tìm kiếm, lọc theo vai trò/trạng thái, xem danh sách và chi tiết tài khoản (Email & SĐT hiển thị trực tiếp).
2. **Alternative Flow A1: Moderate Account (Kiểm duyệt tài khoản):**
   - **Cảnh cáo (Warn):** Ghi lý do, gửi thông báo và lưu Audit Log.
   - **Tạm khóa (Suspend):** Nhập lý do + thời hạn (3, 7, 14, 30 ngày) -> Chuyển trạng thái sang `suspended` và **hủy ngay lập tức toàn bộ phiên đăng nhập (Session/Token)**.
   - **Cấm vĩnh viễn (Ban):** Chuyển sang `banned`, tước quyền và hủy toàn bộ phiên đăng nhập.
   - **Kích hoạt lại (Reactivate):** Mở khóa tài khoản trở lại `active`.
3. **Alternative Flow A2: Send Password Reset Link (Hỗ trợ khôi phục mật khẩu):**
   - Bắt buộc kiểm tra mã **Support Ticket ID** (Định dạng `TK-XXXXX`).
   - Tạo token bảo mật đơn kỳ (Single-use token) và gửi email đặt lại mật khẩu.
4. **Alternative Flow A3: Manual Verification (Xác minh danh tính thủ công):**
   - Bắt buộc kiểm tra mã **Support Ticket ID** + Ghi chú tài liệu đối soát.
   - Đánh dấu xác thực `email_verified_at` và doanh nghiệp `verified`.
5. **Alternative Flow A4: Update Login Email (Cập nhật email đăng nhập):**
   - Bắt buộc kiểm tra mã **Support Ticket ID** + Kiểm tra trùng lặp email mới.
   - Hủy phiên đăng nhập cũ để người dùng đăng nhập bằng email mới.
6. **Alternative Flow A5: Exception Handling (Xử lý ngoại lệ):**
   - Không tìm thấy tài khoản phù hợp -> Hiển thị thông báo thân thiện.
   - Dữ liệu không hợp lệ -> Báo lỗi cụ thể (validation error).
   - Mã Support Ticket sai định dạng -> Từ chối yêu cầu.
   - Cố tình thao tác với tài khoản Admin khác (đặc quyền ngang hoặc cao hơn) -> Chặn và từ chối.
7. **Security & Session Control:**
   - Vô hiệu hóa phiên đăng nhập khi Suspend/Ban. Admin không xem được mật khẩu của người dùng.
   - **Auditability:** Mọi thao tác đổi trạng thái và khôi phục đều được lưu trữ vào bảng `audit_logs` bất biến.

---

## 🌳 II. Màn Hình Dashboard (Thống kê & Biểu đồ xu hướng)

- [x] **5 Chỉ số tổng quan:** Tổng số Candidate, Tổng số Employer, Job Post đang hoạt động, Job Post chờ duyệt, Tổng lượt Application.
- [x] **3 Biểu đồ phân tích xu hướng (Chart.js):**
  - Biểu đồ tăng trưởng người dùng theo thời gian (Candidate vs Employer qua các tháng).
  - Biểu đồ số lượng tin tuyển dụng mới theo thời gian.
  - Biểu đồ tỷ lệ phân bố ngành nghề tuyển dụng.

---

## 🧪 III. Kết Quả Kiểm Thử Tự Động (QA)

- [x] `tests/Feature/Admin/UserManagementTest.php` (12 tests)
- [x] `tests/Feature/Admin/DashboardTest.php` (2 tests)
- [x] Toàn bộ test suite dự án: **39/39 tests PASSED (114 assertions, 0 errors, 0 failures)**.

---

## 🔑 Hướng dẫn đăng nhập trải nghiệm
- **Đường dẫn:** [http://localhost:8000/login](http://localhost:8000/login)
- **Tài khoản Admin:** `admin@eaut.edu.vn`
- **Mật khẩu:** `password`
- **Dashboard xem biểu đồ:** [http://localhost:8000/admin/dashboard](http://localhost:8000/admin/dashboard)
- **Quản lý người dùng:** [http://localhost:8000/admin/users](http://localhost:8000/admin/users)
