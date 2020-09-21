# Patricia-Code-Test

Patricia-Code-Test is my attempt at solving Patricia Coding Test. This is a basic test of making and handling POST requests on edge and embedded devices.

## Basic Explanation

This codebase implements several concept involved in a fast, efficient and safe POST request in a basic embedded systems setting. It includes a system of recurring authentications with refresh token feature to reduce to surface of any attack that might otherwise invoked on the system. The basic features include

- Basic Authentication of Device Using Unique identifier
- Use of Refresh Token for re-Authorization

## Getting Started

To run this code, you require only `composer` and `pip`(Python3) installed on your PC.

Clone this repository and install the required dependencies using [composer](https://getcomposer.org/doc/00-intro.md) and [pip](https://pip.pypa.io/en/stable/) respectively.

### Pull Git Repository.

```bash
mkdir AwesomeCandidateSubmission
cd AwesomeCandidateSubmission
git clone `https://github.com/teezzan/Patricia-Code-Test.git
cd Patricia-Code-Test

```

### Install Dependencies.

Use the package manager [pip](https://pip.pypa.io/en/stable/) to install requests.

```bash
#requires Python3
pip install requests
#or Depending on your PC.
pip3 install requests
```

You will also need to install the needed PHP dependencies by typing the following(assuming that you are in `Patricia-Code-Test` directory)

```bash
cd server
composer install
```

You are set.

### Run Server

This can be done with the following commands from the `Patricia-Code-Test/server` directory.

```bash
php -S localhost:8000
```

### Run Client

This should be done from a separate Terminal Instance. Simply run
the following from the `Patricia-Code-Test/pythonClient` directory.

```bash
python client.py
#or depending on your Machine
python3 client.py
```

## License

[MIT](https://choosealicense.com/licenses/mit/)
