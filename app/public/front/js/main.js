const form = document.getElementById("student-form");

const fields = {
  firstName: {
    input: document.getElementById("first-name"),
    error: document.getElementById("first-name-error"),
  },
  lastName: {
    input: document.getElementById("last-name"),
    error: document.getElementById("last-name-error"),
  },
  email: {
    input: document.getElementById("email"),
    error: document.getElementById("email-error"),
  },
  password: {
    input: document.getElementById("password"),
    error: document.getElementById("password-error"),
  },
};

const successCount = document.getElementById("success-count");
const failCount = document.getElementById("fail-count");

let success = 0;
let fail = 0;

function setError(field, message) {
  field.input.classList.add("invalid");
  field.error.textContent = message;
}

function clearError(field) {
  field.input.classList.remove("invalid");
  field.error.textContent = "";
}

function validate() {
  let isValid = true;

  for (const field of Object.values(fields)) {
    clearError(field);
  }

  if (fields.firstName.input.value.trim() === "") {
    setError(fields.firstName, "Укажите имя");
    isValid = false;
  }

  if (fields.lastName.input.value.trim() === "") {
    setError(fields.lastName, "Укажите фамилию");
    isValid = false;
  }

  const email = fields.email.input.value.trim();
  if (email === "") {
    setError(fields.email, "Укажите email");
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    setError(fields.email, "Некорректный email");
    isValid = false;
  }

  if (fields.password.input.value.length < 8) {
    setError(fields.password, "Пароль должен быть не короче 8 символов");
    isValid = false;
  }

  return isValid;
}

function bump(element, value) {
  element.textContent = value;
  element.classList.remove("pop");
  void element.offsetWidth;
  element.classList.add("pop");
}

form.addEventListener("submit", function (event) {
  event.preventDefault();

  if (validate()) {
    success = success + 1;
    bump(successCount, success);

    for (const field of Object.values(fields)) {
      clearError(field);
    }
    form.reset();
  } else {
    fail = fail + 1;
    bump(failCount, fail);
  }
});

for (const field of Object.values(fields)) {
  field.input.addEventListener("input", function () {
    clearError(field);
  });
}
