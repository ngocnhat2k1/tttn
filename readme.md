Cài đặt những thứ cần thiết để có thể chạy được dự án: <br>
* Cài đặt NodeJS về máy ở địa chỉ https://nodejs.org/en/ <br>
* Mở dự án, bật terminal và chạy các lệnh sau: <br>
    ** Chạy dự án ở client: <br>
      - Chạy: **cd client** để chuyển đường dẫn sang thư mục client <br>
      - Chạy: **npm i** để khởi tạo thư mục node_modules ở client <br>
      - Chạy: **npm start** để chạy webside <br>
    ** Chạy dự án ở Vendor: <br>
      - Chạy: **cd vendor** để chuyển đường dẫn sang thư mục vendor <br>
      - Chạy: **npm i** để khởi tạo thư mục node_modules ở vendor <br>
      - Chạy: **npm start** để chạy webside <br>

Đối với Laravel thì cần thực hiện những việc sau trước khi chạy dự án: <br>
<b>** Đây là phiên bản Laravel 9 **</b> <br>
<h1>CHÚ Ý: Nếu đã cài đặt composer cho PHP 8.1.10 thì bỏ qua 2 bước đầu tiên</h1><br>
      - Tải PHP 8.1.10 phiên bản non threaded và composer <br>
      - Cài đặt composer và chọn PHP phiên bản vừa tải về và tiến hành cài đặt <br>
      - Mở **cmd** hoặc **git bash** tại thư mục đó và chạy: **cd api\SunnyFlowerShop** để chuyển hướng sang thư mục api <br>
      - Chạy: **composer install** để cài đặt các phần còn thiếu của framework <br>
      - Tạo: file **.env** bằng cách copy file **.env.example** và sửa tên lại <br>
      - Chạy: **php artisan key:generate** để tạo APP_KEY trong file **.env** <br>
      - Thay đổi tên database thành **SunnyFlowerShop** <br>
      - Chạy: **php artisan mirgate** hoặc **php artisan mirgate --seed** để tạo dữ liệu seed kèm theo database <br>
      - Nếu trong quá trình chạy migrate gặp vấn đề mà muốn refresh lại thì chỉ cần thêm **:refresh** hoặc **:fresh** ở phía sau **....migrate...** là được. <br>
