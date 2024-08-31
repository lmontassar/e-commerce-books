# Online Bookstore E-Commerce Platform

Welcome to the **Online Bookstore E-Commerce Platform**. This project is a functional and reliable solution for buying books online, developed with a focus on robust logic and technology integration. Built with Symfony and powered by MySQL, this platform was designed to operate flawlessly, ensuring a smooth user experience with no errors. This project was completed in just 18 hours of intense work.

## Features

### User Authentication & Management
- **Registration Verification:** Secure account creation with email verification to ensure authenticity.
- **Password Reset:** Users can easily reset their passwords via a secure link sent to their registered email.
- **Cart Functionality:** Users can add, update, and remove items from their shopping cart before proceeding to checkout.
- **Stripe Integration:** Secure and efficient online payments powered by Stripe.

### Browsing & Searching
- **Categories:** Books are organized into categories, making it easier for users to find what they're looking for.
- **Search with Filters:** Advanced search functionality with filters for genre, author, price range, and more.
- **Pagination:** Efficient browsing with pagination implemented on the home page and search results.

### Administration & Analytics
- **Admin Dashboard:** A powerful dashboard for managing books, categories, users, and monitoring payment history.
- **Google Charts:** Integrated Google Charts for visualizing sales data, user activity, and other key metrics.
- **CRUD Operations:** Comprehensive CRUD functionalities for managing categories, books, payment history, and user data.

### Additional Functionalities
- **Order Management:** Easy tracking and management of orders with delivery options.
- **Payment History:** A detailed log of all transactions for both users and administrators.

## Technology Stack
- **Backend:** Symfony PHP framework
- **Database:** MySQL
- **Payment Gateway:** Stripe
- **Charts & Analytics:** Google Charts

## Focus & Approach

This project emphasizes functionality over design, prioritizing a smooth, error-free experience over visual aesthetics. The development was centered on ensuring that all technological components work correctly, with a strong focus on backend logic and integration. The entire project was developed within 18 hours, showcasing a commitment to delivering a functional product in a short timeframe.

## Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/lmontassar/e-commerce-books

2. **Install dependencies**
   ```bash
   composer install

3. **Setup the database**
   - Create a new MySQL database and update the '.env' file with your database credentials
   - Run the migrations to set up the database schema:
   ```bash
   php bin/console doctrine:migrations:migrate

4. **Run the server**
   ```bash
   php bin/console doctrine:migrations:migrate


## Screenshots

**Login**
![login page](https://github.com/lmontassar/e-commerce-books/blob/main/images/login.PNG)


**Sign up**
![signup page](https://github.com/lmontassar/e-commerce-books/blob/main/images/sign_up.PNG)


**Home page 1**
![Home page](https://github.com/lmontassar/e-commerce-books/blob/main/images/home1.PNG)


**Home page 2**
![Home page](https://github.com/lmontassar/e-commerce-books/blob/main/images/home2.PNG)


## Cart and Payment Functionality
This video demonstrates the cart and payment functionality within the application. reviewing the cart contents, and completing the payment process. Additionally, it shows how users can access their payment history, ensuring transparency and ease of tracking previous transactions.

https://github.com/user-attachments/assets/d323358f-46c7-43f7-8384-a1a6d7af9a71




## Article Video
This video provides an overview of the article page functionality, detailing how users can navigate through articles, add books to their cart directly from the article page, and continue with the purchasing process. It highlights the seamless integration between content exploration and e-commerce features.

https://github.com/user-attachments/assets/96729bd0-e954-4736-8739-d1749e36e4b5



## Admin part
In this video, the admin panel's comprehensive functionality is showcased. It includes all the CRUD operations necessary for managing the platform's content, users, and orders. Additionally, it demonstrates how the admin can utilize Google Charts for statistical analysis and generate reports in Excel format to explain and analyze key metrics effectively.

https://github.com/user-attachments/assets/eaf6f87e-53c7-4053-b9fd-8d3923bd1cf4



#Thank you for your attention, and I hope this project serves as a valuable resource for others.






