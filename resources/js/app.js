require('./bootstrap');

window.currentChannel = null;

$(document).ready(function(){
    //Roomchat stated
    $('.room-list').click(function () {

        const sidebar = document.getElementById('side-bar');
        sidebar.style.display = 'block'; 

        var roomId = $(this).attr('data-id')
        global_room_id = roomId; 
        console.log("Selected Room ID:", global_room_id)
        $('.chat-section').show();

        Echo.private('broadcast-group-message.' + global_room_id)
        .listen('.getRoomChatMessage', (data) => {  
            console.log('Message received:', data);
            const formattedTime = dayjs(data.chat.created_at).format('HH:mm');

            if (user_id != data.chat.user_id && global_room_id == data.chat.room_id) {
                
                let html = `
                    <li class="d-flex justify-content-between mb-4 distance-user-chat" id="${data.chat.id}-chat">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-6.webp" alt="avatar" 
                            class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60">
                        <div class="card mask-custom flex-grow-1" style="min-width: 200px; max-width: 100%;">
                            <div class="card-header d-flex justify-content-between p-3">
                                <p class="fw-bold mb-0">${data.chat.user.name}</p>
                                <p class="text-light small mb-0"><i class="far fa-clock me-2"></i>${formattedTime}</p>
                            </div>
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <p class="mb-0 text-wrap">
                                    ${data.chat.content}   
                                </p>
                                <i class="far fa-trash-alt deleteRoomMessage" aria-hidden="true" data-id = "${data.chat.id}" data-bs-toggle="modal" data-bs-target="#roomdeleteChatModal"></i>
                            </div>
                        </div>
                    </li>`

                    $('#list-chatRoom').append(html);
            }
        })
        .error((err) => {
            console.error('Error while subscribing:', err);
        });

        loadRoomChats()
        loadRoomMembers()

    });

    Echo.private('rooms-load')
    .listen('.roomchatLoad', (event) => {  
        console.log('Message received:', event);
        let html = '';
        if (event.rooms && event.rooms.length > 0) {  
            event.rooms.forEach(function(room) {
                html += `
                    <li class="p-2 border-bottom room-list" data-id="${room.id}">
                        <a href="#!" class="d-flex justify-content-between link-light">
                            <div class="d-flex flex-row">
                                <img src="${room.avatar}" alt="avatar" class="rounded-circle d-flex align-self-center me-3 shadow-1-strong" width="60">
                                <div class="pt-1">
                                    <p class="fw-bold mb-0">${room.name}</p>
                                    <p class="small text-white">${room.description}</p>
                                </div>
                            </div>
                            <div class="pt-1">
                                <p class="small text-white mb-1">${room.last_activity}</p>
                            </div>
                        </a>
                    </li>
                `;
            });
            $('#room-list').append(html); 
        }
    })
    .error((err) => {
        console.error('Error while subscribing:', err);
    });

    //Chat room script
    $('#createRoomForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '/create-room',
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                var createRoomModal = new bootstrap.Modal(document.getElementById('createRoomModal'));
                createRoomModal.hide();

                // Set the room ID in the hidden input field in the add member form
                $('#room_id').val(res.room_id);

                $('#addMemberModal').modal('show');

                // Trigger the add member modal after the room is created
            },
            error: function(xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage);
            }
        });
    });
    

    $('#addMemberForm').submit(function(e){
        e.preventDefault()

        var formData = $(this).serialize()

        $.ajax({
            url:"/add-member",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $('#addMemberModal').modal('hide')
                $('#addMemberForm')[0].reset()
                alert(res.msg)
            },
            error: function(xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage)
            }
        })
    })

    $('#room-chat-form').submit(function (e) {
        e.preventDefault();
    
        var message = $('#room-message').val();
    
        // Kiểm tra giá trị của message trước khi gửi
        console.log("Message being sent:", message);
    
        if (!message || message.trim() === '') {
            console.error("Message is empty or undefined!");
            return; // Nếu message rỗng hoặc undefined, không gửi
        }
    
        $.ajax({
            url: "/save-room-chat",
            type: "POST",
            data: (() => {
                const formData = new FormData();
    
                formData.append('room_id', global_room_id);
                formData.append('message', message); // Gửi message đã kiểm tra
    
                // Handle file upload nếu có
                const fileInput = $('#file-upload')[0];
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    console.log('File details:', {
                        name: fileInput.files[0].name,
                        size: fileInput.files[0].size,
                        type: fileInput.files[0].type
                    });
                    formData.append('file', fileInput.files[0]);
                }
    
                return formData;
            })(),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (res) {
                console.log("Response data:", res);  // Log toàn bộ dữ liệu trả về từ server
                // Clear form inputs after successful submission
                $('#room-message').val('');
                $('#file-upload').val('');
                // Clear any preview image if it exists
                $('.preview-image').remove();
            },
            error: function (xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage);
            }
        });
    });
    
        
    
    function loadRoomChats(){
        $('#list-chatRoom').html('')

        $.ajax({
            url: "load-room-chats",
            type:"POST",
            data: {
                room_id: global_room_id,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                console.log(res)

                let chats = res.chats;
                let html = ''; 
                console.log(chats)
                for(let i = 0; i< chats.length; i++){
                    const formattedTime = dayjs(chats[i].user.created_at).format('HH:mm');
                    if(chats[i].user_id != user_id){
                        html = `
                            <li class="d-flex justify-content-between mb-4 distance-user-chat" id="${chats[i].id}-chat">
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-6.webp" alt="avatar" 
                                    class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60">
                                <div class="card mask-custom flex-grow-1" style="min-width: 200px; max-width: 100%;">
                                    <div class="card-header d-flex justify-content-between p-3">
                                        <p class="fw-bold mb-0">${chats[i].user.name}</p>
                                        <p class="text-light small mb-0"><i class="far fa-clock me-2"></i>${formattedTime}</p>
                                    </div>
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <p class="mb-0 text-wrap">
                                            ${chats[i].content}        
                                        </p>
                                        <i class="far fa-trash-alt deleteRoomMessage" aria-hidden="true" data-id = "${chats[i].id}" data-bs-toggle="modal" data-bs-target="#roomdeleteChatModal"></i>
                                    </div>
                                </div>
                            </li> `

                            $('#list-chatRoom').append(html);
                    }   
                    else{
                        html = `
                            <li class="d-flex justify-content-between mb-4 current-user-chat" id="${chats[i].id}-chat">
                                <div class="card mask-custom w-100">
                                <div class="card-header d-flex justify-content-between p-3"
                                    style="border-bottom: 1px solid rgba(255,255,255,.3);">
                                    <p class="fw-bold mb-0"> Bạn </p>
                                    <p class="text-light small mb-0"><i class="far fa-clock me-2"></i>${formattedTime}</p>
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <p class="mb-0">
                                        ${chats[i].content}
                                    </p>
                                    <i class="far fa-trash-alt deleteRoomMessage" aria-hidden="true" data-id = "${chats[i].id}" data-bs-toggle="modal" data-bs-target="#roomdeleteChatModal"></i>                  
                                </div>
                                </div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-5.webp" alt="avatar"
                                class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60">
                            </li>                   
                        ` 
                        $('#list-chatRoom').append(html);
                    }
                }
            },
            error: function(xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage)
            }
        })
    }

    $(document).on('click','.deleteRoomMessage', function(){
        let msg = $(this).parent().text()
        $('#delete-room-message').text(msg)

        $('#delete-room-message-id').val($(this).attr('data-id'))

        Echo.private('room-message-deleted')
        .listen('.roomchatDeleteMessage', (data) => {   
            console.log(data);
            $('#'+data.id+'-chat').remove()
        })
    })

    $('#delete-room-chat-form').submit(function(e){
        e.preventDefault()
        var id = $('#delete-room-message-id').val()

        $.ajax({
            url:"/delete-room-chats",
            type:"POST",
            data: {id:id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                $('#roomdeleteChatModal').modal('hide');

                $('#'+id+'-chat').remove()
            },
            error: function(xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage)
            }
        })
    })

    function loadRoomMembers() {
        $.ajax({
            url: 'room-members',  
            type: 'GET', 
            data: {
                room_id: global_room_id 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            success: function(res) {
                console.log(res)
                $('#members-list').html(''); 
                if (res.roomsMember) {
                    let room = res.roomsMember;
                    let members = room.users;
                    let html = '';

                    $('#showRoomName').text(room.name);
    
                    for (let i = 0; i < members.length; i++) {
                        html += `
                            <div class="d-flex justify-content-between align-items-center" data-room-id="${room.id}" data-member-id="${members[i].id}">
                                <span class="member-name">${members[i].name}</span>
                                <!-- Dấu ba chấm để mở menu xóa -->
                                <button class="btn btn-link more-options" data-id="${members[i].id}">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <!-- Menu Xóa -->
                                <div class="dropdown-menu" aria-labelledby="moreOptionsMenu${members[i].id}" id="moreOptionsMenu${members[i].id}">
                                    <button class="dropdown-item delete-member" data-id="${members[i].id}" data-bs-toggle="modal" data-bs-target="#deleteMemberModal">
                                        Xóa
                                    </button>
                                </div>
                            </div>

                        `;
                    }
                    $('#members-list').append(html);
                }
            },
            error: function(xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage);
            }
        });
    }

    $(document).on('click', '.more-options', function() {
        var memberId = $(this).data('id');
        var menu = $('#moreOptionsMenu' + memberId);
        menu.toggleClass('show'); 
        $('.dropdown-menu').not(menu).removeClass('show');
    });

    $(document).on('click', '.delete-member', function() {
        var memberId = $(this).data('id');
        var roomId = $(this).closest('.d-flex').data('room-id');
    
        // Nếu người dùng chọn xóa chính mình
        if (memberId == user_id) {
            if (confirm('Bạn có chắc chắn muốn rời khỏi phòng?')) {
                $('#confirmDeleteMember').data('member-id', memberId);
                $('#confirmDeleteMember').data('room-id', roomId);
            } else {
                return;
            }
        } else {
            $('#confirmDeleteMember').data('member-id', memberId);
            $('#confirmDeleteMember').data('room-id', roomId);
        }
    });

    $(document).on('click', '#confirmDeleteMember', function() {
        var memberId = $(this).data('member-id');
        var roomId = $(this).data('room-id');
        

        console.log('Room ID:', roomId);  
        console.log('Member ID:', memberId); 
    
        $.ajax({
            url: '/remove-member',
            type: 'POST',
            data: {
                room_id: roomId,
                user_id: memberId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    alert('Xóa thành viên thành công');
    
                    if (memberId == user_id) {
                        console.log('Phần tử nhóm cần xóa:', $('div[data-id="' + roomId + '"]'));  
                        $('li[data-id="' + roomId + '"]').remove(); 
                        alert('Bạn đã rời khỏi nhóm này');
                    } else {
                        $('[data-room-id="' + roomId + '"][data-member-id="' + memberId + '"]').remove();
                    }
    
                    $('#deleteMemberModal').modal('hide'); 
    
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                }
            },
            error: function(xhr) {
                alert('Lỗi: ' + xhr.responseText);
            }
        });
    }); 

});



