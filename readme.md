Cài đặt những thứ cần thiết để có thể chạy được dự án: <br>
* cài đặt NodeJS về máy ở địa chỉ https://nodejs.org/en/ <br>
* mở dự án, bật terminal và chạy các lệnh sau: <br>
    ** chạy dự án ở client: <br>
      - chạy: **cd client** để chuyển đường dẫn sang thư mục client <br>
      - chạy: **npm i** để khởi tạo thư mục node_modules ở client <br>
      - chạy: **npm start** để chạy webside <br>
    ** chạy dự án ở Vendor: <br>
      - chạy: **cd vendor** để chuyển đường dẫn sang thư mục vendor <br>
      - chạy: **npm i** để khởi tạo thư mục node_modules ở vendor <br>
      - chạy: **npm start** để chạy webside <br>

Đối với Laravel thì cần thực hiện những việc sau trước khi chạy dự án: <br>
      * Đây là phiên bản Laravel 9 * <br>
      - chạy: **cd api\SunnyFlowerShop** để chuyển hướng sang thư mục api <br>
      - chạy: **composer install** để cài đặt các phần còn thiếu của framework <br>
      - tạo: file **.env** bằng cách copy file **.env.example** và sửa tên lại <br>
      - chạy: **php artisan key:generate** để tạo APP_KEY trong file **.env** <br>
      - thay đổi tên database thành "SunnyFlowerShop" <br>
      - chạy: **php artisan mirgate** hoặc **php artisan mirgate --seed** để tạo dữ liệu seed kèm theo database <br>
      - nếu trong quá trình chạy migrate gặp vấn đề mà muốn refresh lại thì chỉ cần thêm **:refresh** hoặc **:fresh** ở phía sau **....migrate...** là được. <br>
