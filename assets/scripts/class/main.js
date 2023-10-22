'use strict';

const json = [{
    type: 'text',
    role: 'to',
    level: 0,
    message: 'Olá, bom dia!',
},
{
    type: 'text',
    role: 'to',
    level: 0,
    message: 'Me chamo Rafael e sou um atendimento automátizado.',
},
{
    type: 'text',
    role: 'to',
    level: 0,
    message: 'Como podemos ajudar:\n 1 - Cancelar produto\n 2 - Aalterar endereço\n',
    answers: [
        [{
            type: 'text',
            role: 'to',
            level: 1,
            message: "Claro, se é isso mesmo que deseja, vamos cancelar seu produto.",
            answer: "1",
        },
        {
            type: 'text',
            role: 'to',
            level: 1,
            message: "Para prosseguir, por gentileza informe seu nome.",
            answer: "1",
            action: "name",
        },
        {
            type: 'text',
            role: 'to',
            level: 1,
            message: "Agora seu email.",
            answer: "1",
            action: "email",
        }],
        [{
            type: 'text',
            role: 'to',
            level: 1,
            message: "Por gentileza digite seu novo endereço.",
            answer: "2",
            action: "email",
        }]
    ],    
}];

class ChatLocalStorage {
    static set(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }
  
    static get(key) {
        const storedValue = localStorage.getItem(key);
        return storedValue ? JSON.parse(storedValue) : null;
    }
  
    static remove(key) {
        localStorage.removeItem(key);
    }
  }

class ChatCookies{
    static set(cname, cvalue, cexpires, cpath = '/') {
        let cookie = `${cname}=${cvalue}`;
        let now = new Date();
        let time = now.getTime();
        let expireTime = time + 1000*cexpires;
            now.setTime(expireTime);
    
            document.cookie = `${cookie};expires=${now.toUTCString()};path=${cpath}`;
    }

    static get(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let cookies = decodedCookie.split(';');
    
        for(let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i];
    
            while (cookie.charAt(0) == ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) == 0) {
                return cookie.substring(name.length, cookie.length);
            }
        }

        return false;
    }

    static forget(cname){
        document.cookie = `${cname}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
    }
}

class ChatBooster{
    static init(){
        document.querySelectorAll('[data-chat-open]').forEach((element) => {
            element.addEventListener('click', (event) => {
                const button = event.target.getAttribute('data-chat-open') 
                    ? event.target : event.target.parentNode;
                const dataChat = button.getAttribute('data-chat-open') 
                    ? button.getAttribute('data-chat-open') 
                    : button.parentNode.getAttribute('data-chat-open');
            
                this.open(dataChat);
                this.moreActions();
            });
        });
    }

    static open(selector) {
        const element = document.querySelector(`[data-chat-content="${selector}"]`);

        element.setAttribute('data-message-box', true);

        this.close(selector);
    }

    static close(selector){
        document.querySelectorAll('[data-chat="close"]').forEach((element) => {
            element.addEventListener('click', () => {
                const element = document.querySelector(`[data-chat-content="${selector}"]`);
    
                element.setAttribute('data-message-box', false);
    
                setTimeout(() => {
                    element.removeAttribute('data-message-box');
                }, 600);
            });
        });
    }

    static moreActions() {
        const moreActions = document.querySelector('[data-more-actions="button"]');

        if(moreActions){
            moreActions.addEventListener('click', () => {
                const moreActions = document.getElementById('more-actions');
            
                if(moreActions.getAttribute('data-more-actions') && moreActions.getAttribute('data-more-actions') == 'true'){
                    moreActions.setAttribute('data-more-actions', false);
            
                    setTimeout(() => {
                        moreActions.removeAttribute('data-more-actions');
                    }, 500);
                }else{
                    moreActions.setAttribute('data-more-actions', true);
                }
            });
        }
    }
}
ChatBooster.init();

class ChatFetch {
    static send(route, method, data) {
        return new Promise((resolve, reject) => {
            fetch(route, {
                method: method,
                body: data
            })
            .then((response) => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Request failed!');
                }
            })
            .then((data) => {
                resolve(data);
            })
            .catch((error) => {
                reject(error);
            });
        });
    }
}

class ChatMessage{
    static create(message, role, timeout, chatSelector, createdAt = null){
        const contentMessage = document.querySelector(`[data-chat-messages="${chatSelector}"]`);

        setTimeout(() => {
            const container = document.createElement('div');
            container.setAttribute('class', `message message-${role}`);

            const boxOne = document.createElement('div');
            boxOne.setAttribute('class', 'message-content');

            const spanMessage = document.createElement('span');
            spanMessage.innerText = message;

            boxOne.appendChild(spanMessage);
    
            const boxTwo = document.createElement('div');
            boxTwo.setAttribute('class', 'message-statustime');

            const spanTime = document.createElement('span');
            spanTime.innerText = this.getTime(createdAt);

            boxTwo.appendChild(spanTime);

            if(role === 'from'){
                let icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-all" viewBox="0 0 16 16">';
                icon += '<path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486-.943 1.179z"/>';
                icon += '</svg>';

                boxTwo.innerHTML += icon;
            }

            boxOne.appendChild(boxTwo);
            container.appendChild(boxOne);
            contentMessage.appendChild(container);

            this.scrollToBottom(contentMessage.parentNode);
        }, timeout);
    }

    static scrollToBottom(container){
        container.scrollTop = container.scrollHeight - container.offsetHeight;
    }

    static getTime(createdAt){
        const date = createdAt === null ? new Date() : new Date(createdAt);
        let hour = date.getHours();
        let minutes = date.getMinutes();

        hour = hour < 10 ? `0${hour}` : hour;
        minutes = minutes < 10 ? `0${minutes}` : minutes;

        return `${hour}:${minutes}`;
    }
}

class ChatMessages{
    constructor(chatSelector){
        this.chatSelector = chatSelector;
        this.form = document.querySelector(`[data-chat-form="${chatSelector}"]`);
        this.contentMessage = document.querySelector(`[data-chat-messages="${chatSelector}"]`);
        this.buttonSend = this.form.querySelector('button[type="submit"]');
        this.inputMessage = this.form.querySelector('input[name="message"]');
    }

    enableForm(){
        this.form.querySelector('input').disabled = false;

        if(this.form.querySelector('[data-more-actions="button"]')){
            this.form.querySelector('[data-more-actions="button"]').disabled = false;
        }

        return this;
    }

    loadChat(){
        const from = this.contentMessage.getAttribute('data-chat-from');
        const to = this.contentMessage.getAttribute('data-chat-to');

        const formData = new FormData();

        formData.append('to', to);
        formData.append('from', from);
        formData.append('method', 'showLead');

        ChatFetch.send('/chat', 'POST', formData)
            .then((response) => {
                if(response.code >= 200 && response.code < 300) {
                    const messages = response.data.messages;

                    messages.forEach((item) => {
                        const origin = item.from == from ? 'from' : 'to';
                        ChatMessage.create(item.message, origin, 0, 'chat', item.created_at);
                    });

                    this.loadChatAdmin();
                }else{
                    ChatAlert.danger(response.message);
                }
            });

        return this;
    }

    setUser(user = null){
        const userId = user === null ? ChatLocalStorage.get('chat_user_id') : user;

        if(userId !== null){
            this.contentMessage.setAttribute('data-chat-from', userId);
        }

        return this;
    }

    submit(){
        this.toggleButtonSend();

        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            const message = this.inputMessage.value;
            const from = this.contentMessage.getAttribute('data-chat-from');
            const to = this.contentMessage.getAttribute('data-chat-to');

            this.saveMessage(from, to, message, (response) => {
                ChatMessage.create(response.data.message, 'from', 0, this.chatSelector);

                this.inputMessage.value = '';
                this.buttonSend.setAttribute('disabled', '');

                if(ChatLocalStorage.get('chat_user_id') === null){
                    ChatLocalStorage.set('chat_user_id', parseInt(response.data.from));
                    this.setUser(response.data.from);
                }
            });
        });
    }

    toggleButtonSend(){
        this.inputMessage.addEventListener('input', (event) => {
            if(event.target.value.length === 0){
                this.buttonSend.setAttribute('disabled', '');
            }else{
                this.buttonSend.removeAttribute('disabled');
            }
        });
    }

    loadChatAdmin(){
        const chat = this.getChat();

        // chat.forEach((item, indice) => {
        //     const from = this.contentMessage.getAttribute('data-chat-to');
        //     const to = this.contentMessage.getAttribute('data-chat-from');

        //     ChatLocalStorage.set('chat_position', indice);
        //     ChatLocalStorage.set('chat_level', item.level);

        //     this.saveMessage(from, to, item.message, (response) => {
        //         ChatMessage.create(response.data.message, 'to', 0, this.chatSelector);
        //         console.log(response)

        //         if(ChatLocalStorage.get('chat_user_id') === null){
        //             ChatLocalStorage.set('chat_user_id', parseInt(response.data.to));
        //             this.setUser(response.data.to);
        //         }
        //     });
        // });
    }

    getChat(){
        // ChatLocalStorage.set('chat_position', 0);
        // ChatLocalStorage.set('chat_level', 0);

        const position = ChatLocalStorage.get('chat_position') && 0;
        const level = ChatLocalStorage.get('chat_level') && 0;

        json.forEach((item, indice) => {
            console.log(item);
        });
    }

    saveMessage(from , to, message, callback) {
        const formData = new FormData();

        formData.append('to', to);
        formData.append('from', from);
        formData.append('message', message);
        formData.append('method', 'saveChat');

        ChatFetch.send('/chat', 'POST', formData)
            .then((response) => {
                if(response.code >= 200 && response.code < 300) {
                    callback(response);
                }else{
                    ChatAlert.danger(response.message);
                }
            });
    }
}
const boxSettings = new ChatMessages('settings');
boxSettings.submit();

const boxChat = new ChatMessages('chat');
boxChat.setUser().loadChat().enableForm().submit();

class ChatDatabase{
    static init(){
        this.reset();
        const form = document.querySelector('[data-form="database-settings"]');

        if(form){
            form.addEventListener('submit', (event) => {
                event.preventDefault();
    
                const dbName = form.querySelector('#db_name');
                const dbUser = form.querySelector('#db_user');
                const dbPassword = form.querySelector('#db_password');
                const dbHost = form.querySelector('#db_host');
                const dbPort = form.querySelector('#db_port');
    
                if(this.validate(dbName, dbUser, dbHost, dbPort)){
                    const formData = new FormData();
    
                    formData.append('db_name', dbName.value);
                    formData.append('db_user', dbUser.value);
                    formData.append('db_password', dbPassword.value);
                    formData.append('db_host', dbHost.value);
                    formData.append('db_port', dbPort.value);
                    formData.append('method', 'createFileSetting');
    
                    ChatFetch.send('/chat', 'POST', formData)
                        .then((response) => {
                            if(response.code >= 200 && response.code < 300) {
                                window.location.reload();
                            }
                        });
                };
            });
        }
    }

    static reset(){
        const button = document.querySelector('[data-chat-menu="settings-database"]');

        if(button){
            button.addEventListener('click', () => {
                const formData = new FormData();
                formData.append('method', 'resetDatabaseSettings');

                ChatFetch.send('/chat', 'POST', formData)
                    .then((response) => {
                        if(response.code >= 200 && response.code < 300) {
                            window.location.reload();
                        }
                    });
            });
        }
    }

    static validate(dbName, dbUser, dbHost, dbPort){
        let valid = true;

        if(dbName.value === ''){
            valid = false;
            dbName.style.border = '1px solid red';
        }else{
            dbName.style.border = '1px solid #25D366';
        }

        if(dbUser.value === ''){
            valid = false;
            dbUser.style.border = '1px solid red';
        }else{
            dbUser.style.border = '1px solid #25D366';
        }

        if(dbHost.value === ''){
            valid = false;
            dbHost.style.border = '1px solid red';
        }else{
            dbHost.style.border = '1px solid #25D366';
        }

        if(dbPort.value === ''){
            valid = false;
            dbPort.style.border = '1px solid red';
        }else{
            dbPort.style.border = '1px solid #25D366';
        }

        return valid;
    }
}
ChatDatabase.init();

class ChatMigrate{
    static init(){
        const button = document.querySelector('[data-chat="migrate"]');

        if(button){
            button.addEventListener('click', () => {
                const formData = new FormData();

                formData.append('method', 'runMigrations');

                ChatFetch.send('/chat', 'POST', formData)
                    .then((response) => {
                        if(response.code >= 200 && response.code < 300) {
                            window.location.reload();
                        }
                    });
            });
        }
    }
}
ChatMigrate.init();

class ChatSettings{
    static init(){
        const form = document.querySelector('[data-form="settings"]');

        if(form){
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                const id = form.querySelector('#id');
                const chat = form.querySelector("#chat"); 
                const name = form.querySelector('#name');
                const avatar = form.querySelector('#avatar');

                if(this.validate(name, avatar)){
                    const formData = new FormData();

                    formData.append('id', id.value);
                    formData.append('name', name.value);
                    formData.append('avatar', avatar.value);
                    formData.append('chat', chat.value);
                    formData.append('method', 'saveSettings');

                    ChatFetch.send('/chat', 'POST', formData)
                        .then((response) => {
                            if(response.code >= 200 && response.code < 300) {
                                window.location.reload();
                            }
                        });
                }
            });
        }
    }

    static validate(name, avatar){
        let valid = true;

        if(name.value === ''){
            valid = false;
            name.style.border = '1px solid red';
        }else{
            name.style.border = '1px solid #25D366';
        }

        if(avatar.value === ''){
            valid = false;
            avatar.style.border = '1px solid red';
        }else{
            avatar.style.border = '1px solid #25D366';
        }

        return valid;
    }
}
ChatSettings.init();

class ChatMenu{
    static init(){
        const leadsButton = document.querySelector('[data-chat-menu="leads"]');
        const settingsButton = document.querySelector('[data-chat-menu="settings"]');
        const leadsContent = document.querySelector('[data-chat-menu-content="leads"]');
        const settingsContent = document.querySelector('[data-chat-menu-content="settings"]');
        const chatContent = document.querySelector('[data-chat-messages="settings"]');
        const footerForm = document.querySelector('[data-chat-form="settings"]');

        if(leadsButton){
            leadsButton.addEventListener('click', () => {
                leadsButton.setAttribute('data-chat-button', 'active');
                settingsButton.setAttribute('data-chat-button', 'inactive');

                leadsContent.hidden = false;
                settingsContent.hidden = true;
                chatContent.innerHTML = '';
                footerForm.querySelector('input').disabled = true;
                footerForm.querySelector('[data-more-actions="button"]').disabled = true;
            });
        }

        if(settingsButton){
            settingsButton.addEventListener('click', () => {
                leadsButton.setAttribute('data-chat-button', 'inactive');
                settingsButton.setAttribute('data-chat-button', 'active');

                leadsContent.hidden = true;
                settingsContent.hidden = false;
                chatContent.innerHTML = '';
                footerForm.querySelector('input').disabled = true;
                footerForm.querySelector('[data-more-actions="button"]').disabled = true;
            });
        }
    }
}
ChatMenu.init();

class ChatLeads{
    static init(){
        this.loadMore();
        this.show();
    }

    static loadMore(){
        const button = document.querySelector('[data-chat-loadmore]');

        if(button){
            button.addEventListener('click', () => {
                const next = button.getAttribute('data-chat-loadmore');
                const formData = new FormData();
    
                formData.append('page', next);
                formData.append('method', 'loadMore');
    
                ChatFetch.send('/chat', 'POST', formData)
                    .then((response) => {
                        if(response.code >= 200 && response.code < 300) {
                            const data = response.data;
                            this.toggleButton(button, data.next);
                            this.createLeads(data.leads);
                        }
                    });
            });
        }
    }

    static show(){
        const buttons = document.querySelectorAll('[data-chat-leads');
        const leadsContent = document.querySelector('[data-chat-menu-content="leads"]');
        const avatar = document.querySelector('[data-chat-avatar]');
        const name = document.querySelector('[data-chat-name]');
        const loadMore = document.querySelector('[data-chat-loadmore]');
        const chatContent = document.querySelector('[data-chat-messages="settings"]');
        const footerForm = document.querySelector('[data-chat-form="settings"]');

        if(buttons){
            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const to = button.getAttribute('data-chat-leads');
                    const formData = new FormData();
                    const from = 1;

                    formData.append('to', to);
                    formData.append('from', from);
                    formData.append('method', 'showLead');

                    ChatFetch.send('/chat', 'POST', formData)
                        .then((response) => {
                            if(response.code >= 200 && response.code < 300) {
                                this.createPreviousButton(chatContent);
                                const lead = response.data.lead;
                                const messages = response.data.messages;

                                if(loadMore) loadMore.hidden = true;
                                leadsContent.hidden = true;
                                name.innerText = lead.name;
                                footerForm.querySelector('input').disabled = false;
                                footerForm.querySelector('[data-more-actions="button"]').disabled = false;
                                avatar.setAttribute('src', lead.avatar);
                                chatContent.setAttribute('data-chat-to', to);
                                chatContent.setAttribute('data-chat-from', from);

                                messages.forEach((item) => {
                                    const origin = item.from == from ? 'from' : 'to';
                                    ChatMessage.create(item.message, origin, 0, 'settings', item.created_at);
                                });
                            };
                        });
                });
            });
        }
    }

    static toggleButton(button, next){
        if(next){
            button.setAttribute('data-chat-loadmore', next);
        }else{
            button.remove();
        }
    }

    static createLeads(leads){
        const content = document.querySelector('[data-chat="list-leads"]');

        leads.forEach((lead) => {
            const button = document.createElement('button');
            button.setAttribute('data-chat-leads', lead.id);
            button.setAttribute('type', 'button');
            button.setAttribute('title', lead.name);
            button.setAttribute('class', 'chat-leads');

            const div = document.createElement('div');
            div.setAttribute('class', 'chat-leads-user-avatar');

            const img = document.createElement('img');
            img.setAttribute('src', lead.avatar);
            img.setAttribute('alt', lead.name);

            div.appendChild(img);
            button.appendChild(div);

            const p = document.createElement('p');
            p.innerText = lead.name;

            button.appendChild(p);
            content.appendChild(button);
        });

        this.show();
    }

    static createPreviousButton(chatContent){
        chatContent.innerHTML = '';

        const button = document.createElement('button');
        button.setAttribute('data-chat', 'previous');
        button.setAttribute('class', 'chat-message-previous');
        button.setAttribute('type', 'button');
        button.setAttribute('title', 'Voltar');

        let icon = '<xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">';
        icon += '<path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>';
        icon += '</svg>';

        button.innerHTML = icon;

        const span = document.createElement('span');
        span.innerText = 'Voltar';

        button.appendChild(span);
        chatContent.appendChild(button);

        this.previousPage();
    }

    static previousPage(){
        const button = document.querySelector('[data-chat="previous"]');
        const leadsContent = document.querySelector('[data-chat-menu-content="leads"]');
        const avatar = document.querySelector('[data-chat-avatar]');
        const name = document.querySelector('[data-chat-name]');
        const loadMore = document.querySelector('[data-chat-loadmore]');
        const chatContent = document.querySelector('[data-chat-messages="settings"]');
        const footerForm = document.querySelector('[data-chat-form="settings"]');

        if(button){
            button.addEventListener('click', () => {
                leadsContent.hidden = false;
                chatContent.innerHTML = '';
                avatar.src = avatar.getAttribute('data-chat-avatar');
                name.innerHTML = name.getAttribute('data-chat-name');
                footerForm.querySelector('input').disabled = true;
                footerForm.querySelector('[data-more-actions="button"]').disabled = true;

                if(loadMore) loadMore.hidden = false;
            });
        }
    }
}
ChatLeads.init();

class ChatAlert{
    static create(message, type, icon){
        const section = document.createElement('section');
        section.setAttribute('class', `chat-alert chat-alert-${type}`);

        section.innerHTML = icon;;

        const p = document.createElement('p');
        p.innerText = message;

        section.appendChild(p);

        document.querySelector('[data-chat-messages="chat"]').parentNode.appendChild(section);
    }

    static success(message){
        const type = 'success';
        let icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">';
        icon += '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>';
        icon += '</svg>';

        this.create(message, type, icon);
    }

    static info(message){
        const type = 'info';
        let icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">';
        icon += '<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>';
        icon += '</svg>';

        this.create(message, type, icon);
    }

    static wharning(message){
        const type = 'wharning';
        let icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">';
        icon += '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>';
        icon += '</svg>';

        this.create(message, type, icon);
    }

    static danger(message){
        const type = 'danger';
        let icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle-fill" viewBox="0 0 16 16">';
        icon += '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>';
        icon += '</svg>';
            
        this.create(message, type, icon);
    }
}
