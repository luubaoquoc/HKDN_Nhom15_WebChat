window.currentChannel = null;

$(document).ready(function () {
    //Roomchat stated
    $('.room-list').click(function () {

        const sidebar = document.getElementById('side-bar');
        sidebar.style.display = 'block';

        var roomId = $(this).attr('data-id')
        global_room_id = roomId;
        console.log("Selected Room ID:", global_room_id)
        $('.chat-section').show();

        Echo.private('broadcast-group-message.' + global_room_id)
            .listen('getRoomChatMessage', (data) => {
                console.log('Message received:', data);

                if (user_id != data.chat.user_id && global_room_id == data.chat.room_id) {

                    let html = `
                    <li class="d-flex justify-content-between mb-4 distance-user-chat" id="${data.chat.id}-chat">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-6.webp" alt="avatar"
                            class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60">
                        <div class="card mask-custom">
                            <div class="card-header d-flex justify-content-between p-3"
                            style="border-bottom: 1px solid rgba(255,255,255,.3);">
                                <p class="fw-bold mb-0">Brad Pitt</p>
                                <p class="text-light small mb-0"><i class="far fa-clock"></i> Just now</p>
                            </div>
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <p class="mb-0">${data.chat.content}</p>
                            </div>
                        </div>
                    </li>`;

                    $('#list-chatRoom').append(html);
                }
            })
            .error((err) => {
                console.error('Error while subscribing:', err);
            });

        loadRoomChats()
        loadRoomMembers()
        loadPinnedMessages() // Thêm hàm load tin nhắn đã ghim

    });

    //Chat room script
    $('#createRoomForm').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '/create-room',
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                var createRoomModal = new bootstrap.Modal(document.getElementById('createRoomModal'));
                createRoomModal.hide();

                // Set the room ID in the hidden input field in the add member form
                $('#room_id').val(res.room_id);

                $('#addMemberModal').modal('show');

                // Trigger the add member modal after the room is created
            },
            error: function (xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage);
            }
        });
    });


    $('#addMemberForm').submit(function (e) {
        e.preventDefault()

        var formData = $(this).serialize()

        $.ajax({
            url: "/add-member",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $('#addMemberModal').modal('hide')
                $('#addMemberForm')[0].reset()
                alert(res.msg)
            },
            error: function (xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage)
            }
        })
    })

    $('#room-chat-form').submit(function (e) {
        e.preventDefault()

        var message = $('#room-message').val()

        $.ajax({
            url: "/save-room-chat",
            type: "POST",
            data: (() => {
                const formData = new FormData();

                // Log initial values
                console.log('Initial values:', {
                    room_id: global_room_id,
                    message: message,
                });

                formData.append('room_id', global_room_id);

                // Only append message if not empty
                if (message && message.trim() !== '') {
                    formData.append('message', message);
                }

                // Handle file upload
                const fileInput = $('#file-upload')[0];
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    console.log('File details:', {
                        name: fileInput.files[0].name,
                        size: fileInput.files[0].size,
                        type: fileInput.files[0].type
                    });
                    formData.append('file', fileInput.files[0]);
                }

                // Log final FormData contents
                console.log('=== FormData Contents ===');
                for (let pair of formData.entries()) {
                    console.log(`${pair[0]}: `, pair[1] instanceof File ?
                        `File: ${pair[1].name} (${pair[1].type})` :
                        pair[1]
                    );
                }

                return formData;
            })(),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                // 'Content-Type': 'multipart/form-data'

            },
            success: function (res) {
                console.log(res)
                // Clear form inputs after successful submission
                $('#room-message').val('')
                $('#file-upload').val('')
                // Clear any preview image if it exists
                $('.preview-image').remove()

                const formattedTime = dayjs(res.data.created_at).format('HH:mm');

                let image = null;
                let content = res.data.content
                if (res.file_type === 'image') {
                    image = `<img src="${res.data.file_url}" class="img-fluid">`;
                } else if (res.file_type === 'document') {
                    image = `<a href="${res.data.file_url}" target="_blank">${res.data.content}</a>`;
                }

                let html = `
                    <li class="d-flex justify-content-between mb-4 current-user-chat" id="${res.data.id}-chat">
                        <div class="card mask-custom w-100">
                        <div class="card-header d-flex justify-content-between p-3"
                            style="border-bottom: 1px solid rgba(255,255,255,.3);">
                            <p class="fw-bold mb-0">${res.data.user.name}</p>
                            <p class="text-light small mb-0"><i class="far fa-clock me-2"></i>${formattedTime}</p>
                        </div>
                        <div class="card-body d-flex justify-content-between align-items-center gap-2">
                            <div class="mb-0">
                            <div class="mb-0">
                                ${image ? image : ''}
                            </div>
                            <div class="mb-0">
                                ${content}
                            </div>
                                </div>
                            <div>
                                <i class="fas fa-thumbtack me-2 pinMessage" aria-hidden="true" data-id="${res.data.id}" data-pinned="false"></i>
                                <i class="far fa-trash-alt deleteRoomMessage" aria-hidden="true" data-id="${res.data.id}" data-bs-toggle="modal" data-bs-target="#roomdeleteChatModal"></i>
                            </div>
                        </div>
                        </div>
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-5.webp" alt="avatar"
                        class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60">
                    </li>
                `

                $('#list-chatRoom').append(html)
            },
            error: function (xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage)
            }
        })
    })

    function loadRooms() {
        $.ajax({
            url: '/get-rooms',  // Đảm bảo API này trả về danh sách nhóm hiện tại của người dùng
            type: 'GET',
            success: function (res) {
                $('#room-list').html('');  // Xóa danh sách nhóm cũ
                if (res.rooms && res.rooms.length > 0) {
                    let html = '';
                    res.rooms.forEach(function (room) {
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
            },
            error: function (xhr) {
                alert('Lỗi khi tải danh sách nhóm: ' + xhr.responseText);
            }
        });
    }


    function loadRoomChats() {
        $('#list-chatRoom').html('')

        $.ajax({
            url: "load-room-chats",
            type: "POST",
            data: {
                room_id: global_room_id,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                console.log(res)

                let chats = res.chats;
                let html = '';
                console.log(chats)
                for (let i = 0; i < chats.length; i++) {
                    const formattedTime = dayjs(chats[i].user.created_at).format('HH:mm');

                    let image = null;
                    let check = isImageFile(chats[i].file_url);

                    if (check) {
                        image = `<img src="${chats[i].file_url}" class="img-fluid">`;
                    } else if (chats[i].file_url && !check) {
                        image = `<a href="${chats[i].file_url}" target="_blank">${res.data.content}</a>`;
                    }

                    if (chats[i].user_id != user_id) {
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

                                         <div class="mb-0">
                            <div class="mb-0">
                                ${chats[i].file_url ? image : ''}
                            </div>
                            <div class="mb-0 text-wrap">
                                 ${chats[i].content}
                            </div>
                                </div>
                                        <div>
                                            <i class="fas fa-thumbtack me-2 pinMessage ${chats[i].is_pinned ? 'text-warning' : ''}" aria-hidden="true" data-id="${chats[i].id}" data-pinned="${chats[i].pinned}"></i>
                                            <i class="far fa-trash-alt deleteRoomMessage" aria-hidden="true" data-id="${chats[i].id}" data-bs-toggle="modal" data-bs-target="#roomdeleteChatModal"></i>
                                        </div>
                                    </div>
                                </div>
                            </li> `

                        $('#list-chatRoom').append(html);
                    } else {
                        html = `
                            <li class="d-flex justify-content-between mb-4 current-user-chat" id="${chats[i].id}-chat">
                                <div class="card mask-custom w-100">
                                <div class="card-header d-flex justify-content-between p-3"
                                    style="border-bottom: 1px solid rgba(255,255,255,.3);">
                                    <p class="fw-bold mb-0">${chats[i].user.name}</p>
                                    <p class="text-light small mb-0"><i class="far fa-clock me-2"></i>${formattedTime}</p>
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center">
                                     <div class="mb-0">
                            <div class="mb-0">
                                 ${chats[i].file_url ? image : ''}
                            </div>
                            <div class="mb-0 text-wrap">
                                 ${chats[i].content}
                            </div>
                                </div>
                                    <div>
                                        <i class="fas fa-thumbtack me-2 pinMessage ${chats[i].is_pinned ? 'text-warning' : ''}" aria-hidden="true" data-id="${chats[i].id}" data-pinned="${chats[i].pinned}"></i>
                                        <i class="far fa-trash-alt deleteRoomMessage" aria-hidden="true" data-id="${chats[i].id}" data-bs-toggle="modal" data-bs-target="#roomdeleteChatModal"></i>
                                    </div>
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
            error: function (xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage)
            }
        })
    }

    function isImageFile(url) {
        if (url) {
            const extension = url.split('.').pop().toLowerCase();
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            return imageExtensions.includes(extension);
        }
        return false;
    }

    $(document).on('click', '.deleteRoomMessage', function () {
        let msg = $(this).parent().text()
        $('#delete-room-message').text(msg)

        $('#delete-room-message-id').val($(this).attr('data-id'))

        Echo.private('room-message-deleted')
            .listen('RoomMessageDeletedEvent', (data) => {
                console.log(data);
                $('#' + data.id + '-chat').remove()
            })
    })

    $('#delete-room-chat-form').submit(function (e) {
        e.preventDefault()
        var id = $('#delete-room-message-id').val()

        $.ajax({
            url: "/delete-room-chats",
            type: "POST",
            data: {id: id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $('#roomdeleteChatModal').modal('hide');

                $('#' + id + '-chat').remove()
            },
            error: function (xhr) {
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
            success: function (res) {
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
            error: function (xhr) {
                var errorMessage = xhr.status === 422 ? Object.values(xhr.responseJSON.errors).join('\n') : 'Có lỗi xảy ra, vui lòng thử lại!';
                alert('Lỗi: \n' + errorMessage);
            }
        });
    }

    $(document).on('click', '.more-options', function () {
        var memberId = $(this).data('id');
        var menu = $('#moreOptionsMenu' + memberId);
        menu.toggleClass('show');
        $('.dropdown-menu').not(menu).removeClass('show');
    });

    $(document).on('click', '.delete-member', function () {
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

    $(document).on('click', '#confirmDeleteMember', function () {
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
            success: function (res) {
                if (res.success) {
                    alert('Xóa thành viên thành công');

                    if (memberId == user_id) {
                        console.log('Phần tử nhóm cần xóa:', $('div[data-id="' + roomId + '"]'));  // Kiểm tra xem phần tử có được chọn đúng không
                        $('li[data-id="' + roomId + '"]').remove();
                        alert('Bạn đã rời khỏi nhóm này');
                    } else {
                        $('[data-room-id="' + roomId + '"][data-member-id="' + memberId + '"]').remove();
                    }

                    $('#deleteMemberModal').modal('hide');

                    loadRooms();

                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                }
            },
            error: function (xhr) {
                alert('Lỗi: ' + xhr.responseText);
            }
        });
    });

    // Thêm sự kiện click cho nút pin
    $(document).on('click', '.pinMessage', function () {
        let messageId = $(this).data('id');
        var $icon = $(this);

        $.ajax({
            url: '/api/pin-message',
            type: 'POST',
            data: {
                message_id: messageId, // ID của tin nhắn
                room_id: global_room_id, // ID của room
                user_id: user_id,
            },

            success: function (res) {
                console.log(res);
                $icon.toggleClass('text-warning');
                if (res.type == 'success') {
                    $(this).toggleClass('text-warning');
                    alert(res.message);
                    loadPinnedMessages();
                }
            },
            error: function (xhr) {
                alert('Lỗi: ' + xhr.responseText);
            }
        });
    });

    // Thêm hàm load tin nhắn đã ghim
    function loadPinnedMessages() {
        $.ajax({
            url: '/api/pin-message?room_id=' + global_room_id,
            type: 'GET',
            success: function (res) {
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(function (item) {
                        const formattedTime = dayjs(item.message.created_at).format('HH:mm');
                        let pinnedHtml = `
                            <div class="pinned-message" id="pinned-${item.message.id}">
                                <div class="card mask-custom">
                                    <div class="card-header">
                                        <strong>${item.message_user.name}</strong>
                                        <small>${formattedTime}</small>
                                    </div>
                                    <div class="card-body">
                                        <p>${item.message.content}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        html += pinnedHtml;
                    });
                }
                $('#pinned-messages').empty().append(html);
            },
            error: function (xhr) {
                console.error('Lỗi khi tải tin nhắn đã ghim:', xhr.responseText);
            }
        });
    }

    fetch($pinned_messages, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message_id: selectedMessageId, // ID của tin nhắn
            room_id: global_room_id, // ID của room
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                console.log('Ghim tin nhắn thành công:', data);
            } else {
                console.error('Lỗi:', data.error);
            }
        })
        .catch(error => {
            console.error('Lỗi không mong muốn:', error);
        });

});
