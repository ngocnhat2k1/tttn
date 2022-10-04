Cài đặt những thứ cần thiết để có thể chạy được dự án:
* cài đặt NodeJS về máy ở địa chỉ https://nodejs.org/en/
* mở dự án, bật terminal và chạy các lệnh sau:
    ** chạy dự án ở client:
      - chạy: **cd client** để chuyển đường dẫn sang thư mục client
      - chạy: **npm i** để khởi tạo thư mục node_modules ở client
      - chạy: **npm start** để chạy webside
    ** chạy dự án ở Vendor:
      - chạy: **cd vendor** để chuyển đường dẫn sang thư mục vendor
      - chạy: **npm i** để khởi tạo thư mục node_modules ở vendor
      - chạy: **npm start** để chạy webside

Đối với Laravel thì cần thực hiện những việc sau trước khi chạy dự án:
      *** Đây là phiên bản Laravel 9 ***
      - chạy: **cd api\SunnyFlowerShop** để chuyển hướng sang thư mục api
      - chạy: **composer install** để cài đặt các phần còn thiếu của framework
      - tạo: file **.env** bằng cách copy file **.env.example** và sửa tên lại
      - chạy: **php artisan key:generate** để tạo APP_KEY trong file **.env**
      - thay đổi tên database thành "SunnyFlowerShop"
      - chạy: **php artisan mirgate** hoặc **php artisan mirgate --seed** để tạo dữ liệu seed kèm theo database
      - nếu trong quá trình chạy migrate gặp vấn đề mà muốn refresh lại thì chỉ cần thêm **:refresh** hoặc **:fresh** ở phía sau **....migrate...** là được.
