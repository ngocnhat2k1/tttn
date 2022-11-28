<h1>FRONTEND</h1><br>
<h3>Cài đặt những thứ cần thiết để có thể chạy được dự án:</h3> <br>
* Cài đặt NodeJS về máy ở địa chỉ https://nodejs.org/en/ <br>
* Mở dự án, bật terminal và chạy các lệnh sau: <br>
    ** Chạy dự án ở client: <br>
      - Chạy: <b>cd client</b> để chuyển đường dẫn sang thư mục client <br>
      - Chạy: <b>npm i</b> để khởi tạo thư mục node_modules ở client <br>
      - Chạy: <b>npm start</b> để chạy webside <br>
    ** Chạy dự án ở Vendor: <br>
      - Chạy: <b>cd vendor</b> để chuyển đường dẫn sang thư mục vendor <br>
      - Chạy: <b>npm i</b> để khởi tạo thư mục node_modules ở vendor <br>
      - Chạy: <b>npm start</b> để chạy webside <br>

<h1>BACKEND</h1> <br>
<h3>** Đây là phiên bản Laravel 9 **</h3> <br>

Để chạy localhost thì chỉ cần sử dụng lệnh <b>php artisan serv</b><br>

<b>Đối với Laravel thì cần thực hiện những việc sau trước khi chạy dự án:</b><br>
<b>CHÚ Ý: Nếu đã cài đặt composer cho PHP 8.1.10 thì bỏ qua 2 bước đầu tiên</b><br>
      - Tải PHP 8.1.10 phiên bản non threaded và composer <br>
      - Cài đặt composer và chọn PHP phiên bản vừa tải về và tiến hành cài đặt <br>
      <b> (Khuyến khích nên sử dụng laragon để thuận tiện trong việc cài đặt và sử dụng Laravel) </b> <br>
      - Mở <b>cmd</b> hoặc <b>git bash</b> tại thư mục clone repository và chạy: <b>cd api\NPCamera</b> để chuyển hướng sang thư mục api <br>
      - Chạy: <b>composer install</b> để cài đặt các phần còn thiếu của framework <br>
      - Tạo: file <b>.env</b> bằng cách copy file <b>.env.example</b> và sửa tên lại <br>
      - Chạy: <b>php artisan key:generate</b> để tạo APP_KEY trong file <b>.env</b> <br>
      - Thay đổi <b>DB_DATABASE</b> thành <b>NPCamera</b> <br>
      - Chạy: <b>php artisan migrate</b> hoặc <b>php artisan migrate --seed</b> để tạo dữ liệu seed kèm theo database <br>
      - Nếu trong quá trình chạy migrate gặp vấn đề mà muốn refresh lại thì chỉ cần thêm <b>:refresh</b> hoặc <b>:fresh</b> ở phía sau <b>....migrate...</b> là được.<br>
      
<strong>Về phần gửi Mail</strong><br>
      - Để test gửi mail, trước tiền bạn cần phải vào trang <b>mailtrap.io</b> và đăng nhập, sau đó vào SMTP Setting ở phần Sandbox<br>
      - Ở phần <b>Integrations</b> chọn <b>Laravel 7+</b><br>
      - Vào file <b>.env</b> và điền theo giống với những gì mà trang <b>mailtrap.io</b> cung cấp ở phần dưới <b>Integrations</b> vào trong file đó từ phần <b>MAIL_MAILER</b> đến <b>MAIL_ENCRYPTION</b><br>
      - Còn phần MAIL_FROM_ADDRESS thì điền bất cứ mail nào cũng được và không nhất thiết là mail đã tồn tại
      - Cuối cùng là đổi tên phần <b>APP_NAME</b> thành tên theo ý muốn (Nếu muốn thêm tên có dấu cách thì nên thêm dấu "" vào để tránh gây báo lỗi)
