# Filament MBI

## Overview

Filament MBI is a medical business intelligence stack built using Laravel and Filament. This project aims to provide a robust and scalable platform for managing various resources, including Chatwoot, Stripe data, analytics, and conversational forms, all within a single system. The current release is an alpha version intended for internal use only.

## Features

- **Laravel and Filament Integration**: Leverages the power of Laravel's Eloquent ORM and Filament's prebuilt components for efficient database interactions and user interface development.
- **Multi-Schema PostgreSQL Database**: Organizes resources into separate schemas for better manageability and isolation.
- **Support for Various Data Sources**:
  - Chatwoot for customer support data
  - Stripe for payment processing data

## Installation

### Prerequisites

- PHP 8.0 or higher
- Composer
- Node.js and NPM
- Docker (for development environment)
- PostgreSQL database with prepared schemas

### Steps

1. **Clone the Repository**:

    ```bash
    git clone https://github.com/yourusername/filament-mbi.git
    cd filament-mbi
    ```

2. **Install PHP Dependencies**:

    ```bash
    composer install
    ```

3. **Install Node.js Dependencies**:

    ```bash
    npm install
    ```

4. **Set Up Environment Variables**:

    Copy the `.env.example` file to `.env` and update the necessary environment variables.

    ```bash
    cp .env.example .env
    ```

5. **Generate Application Key**:

    ```bash
    php artisan key:generate
    ```

6. **Run Migrations**:

    ```bash
    php artisan migrate
    ```

7. **Build Assets**:

    ```bash
    npm run dev
    ```

8. **Run the Development Server**:

    ```bash
    php artisan serve
    ```

## Usage

### Admin Panel

Access the admin panel at `http://localhost:8000/admin` to manage resources and configurations. The admin panel leverages Filament's intuitive interface for seamless management.

### Development

For a complete development environment, use the provided Docker configuration:

1. **Build and Start Docker Containers**:

    ```bash
    docker-compose up --build
    ```

2. **Access the Application**:

    The application will be available at `http://localhost`.

## Contributing

We welcome contributions from the community. Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Commit your changes with a clear message.
4. Push your changes to your fork.
5. Create a pull request against the `develop` branch.

## License

This project is open-source and available under the [MIT License](LICENSE).

## Acknowledgements

We would like to thank the developers and contributors of Laravel, Filament, and other open-source projects that make this stack possible.

---

For any issues or questions, please contact the development team at [support@yourdomain.com].
