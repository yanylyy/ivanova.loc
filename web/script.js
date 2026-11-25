const articles = document.querySelector('.articles');
const form = document.getElementById('articleForm');
const submitBtn = form.querySelector('input[type="submit"]');

const apiBase = 'http://localhost/IV1-23-1.loc/api';
const url = `${apiBase}/articles`;
const userUrl = `${apiBase}/user`;
const loginUrl = `${apiBase}/user/login`;


const guestPanel = document.getElementById('guestPanel');
const loginForm = document.getElementById('loginForm');
const userPanel = document.getElementById('userPanel');
const showLoginBtn = document.getElementById('showLoginBtn');
const cancelLoginBtn = document.getElementById('cancelLoginBtn');
const logoutBtn = document.getElementById('logoutBtn');
const loginError = document.getElementById('loginError');
const userNameText = document.getElementById('userNameText');

let currentUser = null;
let editingArticleId = null;
let editingListElement = null;


function getAuthHeaders(extraHeaders = {}) {
    const headers = { ...extraHeaders };
    if (currentUser && currentUser.authHeader) {
        headers['Authorization'] = currentUser.authHeader;
    }
    return headers;
}


showLoginBtn.addEventListener('click', function () {
    loginForm.classList.remove('hidden');
    guestPanel.classList.add('hidden');
    loginError.textContent = '';
});

cancelLoginBtn.addEventListener('click', function () {
    loginForm.classList.add('hidden');
    guestPanel.classList.remove('hidden');
    loginForm.reset();
    loginError.textContent = '';
});

loginForm.addEventListener('submit', function (event) {
    event.preventDefault();
    loginError.textContent = '';

    const nickname = document.getElementById('loginNickname').value.trim();
    const password = document.getElementById('loginPassword').value;

    fetch(loginUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ nickname, password })
    })
        .then(response => response.json())
        .then(data => {
            console.log('Ответ при входе:', data);
            if (data.user && data.user !== 'No authenticated') {
                const token = btoa(unescape(encodeURIComponent(`${nickname}:${password}`)));
                currentUser = {
                    nickname: data.user,
                    id: Number(data.id),
                    authHeader: `Basic ${token}`
                };

                updateAuthUI();
                loginForm.reset();
                loadArticles();
            } else {
                loginError.textContent = data.error || 'Неправильный логин или пароль';
            }
        })
        .catch(error => {
            console.error('Ошибка входа:', error);
            loginError.textContent = 'Ошибка соединения с сервером';
        });
});


logoutBtn.addEventListener('click', function () {
    currentUser = null;
    editingArticleId = null;
    editingListElement = null;
    document.cookie = 'token=; Max-Age=0; path=/IV1-23-1.loc;';
    document.cookie = 'token=; Max-Age=0; path=/;';

    form.reset();
    submitBtn.value = 'Создать статью';
    updateAuthUI();
    loadArticles();
});

function updateAuthUI() {
    if (currentUser !== null) {
        guestPanel.classList.add('hidden');
        loginForm.classList.add('hidden');
        userPanel.classList.remove('hidden');
        form.classList.remove('hidden');
        userNameText.textContent = currentUser.nickname;
    } else {
        guestPanel.classList.remove('hidden');
        loginForm.classList.add('hidden');
        userPanel.classList.add('hidden');
        form.classList.add('hidden');
        userNameText.textContent = '';
    }
}

function addArticleToPage(item) {
    if (!item || item.error || !item.name) return;

    const listElement = document.createElement('li');
    listElement.classList.add('articles-list');

    const title = document.createElement('h2');
    title.textContent = item.name;

    const img = document.createElement('img');
    img.classList.add('articles-img');
    if (item.img && item.img !== 'null') {
        img.src = item.img.startsWith('blob:') ? item.img : '../' + item.img;
    }

    const text = document.createElement('p');
    text.textContent = item.text;

    listElement.appendChild(title);
    if (item.img && item.img !== 'null') {
        listElement.appendChild(img);
    }
    listElement.appendChild(text);


    if (currentUser !== null && Number(item.author_id) === currentUser.id) {
        const editBtn = document.createElement('button');
        editBtn.textContent = 'Редактировать';
        editBtn.classList.add('btn', 'btn-warning', 'me-2');
        editBtn.type = 'button';

        editBtn.addEventListener('click', function () {
            if (!item.id) {
                alert('Обновите страницу, чтобы редактировать только что созданную статью');
                return;
            }

            console.log('Редактирование статьи ID:', item.id, {
                name: title.textContent,
                text: text.textContent
            });

            document.getElementById('inputName').value = title.textContent;
            document.getElementById('inputText').value = text.textContent;

            editingArticleId = item.id;
            editingListElement = listElement;
            submitBtn.value = 'Сохранить изменения';

            form.scrollIntoView({ behavior: 'smooth' });
        });

        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Удалить';
        deleteBtn.classList.add('btn', 'btn-danger');
        deleteBtn.type = 'button';

        deleteBtn.addEventListener('click', function () {
            if (!item.id) {
                alert('Обновите страницу, чтобы удалить только что созданную статью');
                return;
            }

            fetch(`${url}/${item.id}`, {
                method: 'DELETE',
                headers: getAuthHeaders()
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка сервера при удалении');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Ответ при удалении:', data);
                    if (data.result === 'OK') {
                        listElement.remove();
                    }
                })
                .catch(error => console.error('Ошибка удаления:', error));
        });

        listElement.appendChild(editBtn);
        listElement.appendChild(deleteBtn);
    }

    articles.appendChild(listElement);
}

function loadArticles() {
    articles.innerHTML = '';
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Массив успешно получен:', data);
            if (data && data['articles']) {
                data['articles'].map(addArticleToPage);
            }
        })
        .catch(error => console.error('Ошибка загрузки:', error));
}


fetch(userUrl)
    .then(response => response.json())
    .then(userData => {
        if (userData && userData.id) {
            currentUser = {
                nickname: userData.user,
                id: Number(userData.id),
                authHeader: null
            };
        } else {
            currentUser = null;
        }
    })
    .catch(() => {
        currentUser = null;
    })
    .finally(() => {
        updateAuthUI();
        loadArticles();
    });

form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (currentUser === null) return;

    const nameValue = document.getElementById('inputName').value;
    const textValue = document.getElementById('inputText').value;
    const fileInput = document.getElementById('inputImg');
    const selectedFile = fileInput && fileInput.files ? fileInput.files.item(0) : null;
    const imgValue = selectedFile ? 'uploads/' + selectedFile.name : null;

    const jsonData = {
        articles: [
            {
                name: nameValue,
                text: textValue,
                img: imgValue
            }
        ]
    };

    if (editingArticleId !== null) {
        console.log(jsonData);

        fetch(`${url}/${editingArticleId}`, {
            method: 'PATCH',
            headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
            body: JSON.stringify(jsonData)
        })
            .then(response => response.json())
            .then(data => {
                console.log('Ответ при редактировании:', data);
                if (data.result === 'OK') {
                    editingListElement.querySelector('h2').textContent = nameValue;
                    editingListElement.querySelector('p').textContent = textValue;

                    editingArticleId = null;
                    editingListElement = null;
                    submitBtn.value = 'Создать статью';
                    form.reset();
                } else {
                    alert(data.error || 'Ошибка при сохранении');
                }
            })
            .catch(error => console.error('Ошибка редактирования:', error));
    } else {
        console.log('Отправка при создании:', jsonData);

        const localArticle = {
            name: nameValue,
            text: textValue,
            img: selectedFile instanceof File ? URL.createObjectURL(selectedFile) : null,
            author_id: currentUser.id
        };

        fetch(url, {
            method: 'POST',
            headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
            body: JSON.stringify(jsonData)
        })
            .then(response => response.json())
            .then(data => {
                console.log('Ответ при создании:', data);
                if (data.id) {
                    localArticle.id = data.id;
                }
                addArticleToPage(localArticle);
                form.reset();
            })
            .catch(error => console.error('Ошибка создания:', error));
    }
});
