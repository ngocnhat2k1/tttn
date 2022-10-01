cài đặt những thứ cần thiết để có thể chạy được dự án:
- chạy: **cd client** để chuyển đường dẫn sang thư mục client
- chạy: **npm i** để khởi tạo thư mục node_modules ở client
- chạy: **npm start** để chạy webside

Đối với Laravel thì cần thực hiện những việc sau trước khi chạy dự án:
*** Đây là phiên bản Laravel 9 ***
- chạy: **php artisan key:generate** để tạo APP_KEY trong file env
- thay đổi tên database thành "SunnyFlowerShop" và thêm mật khẩu là 11042001Phong
- chạy: **php artisan mirgate** hoặc **php artisan mirgate --seed** để tạo dữ liệu seed kèm theo database
- nếu trong quá trình chạy migrate gặp vấn đề mà muốn refresh lại thì chỉ cần thêm **:refresh** hoặc **:fresh** ở phía sau **....migrate...** là được.
