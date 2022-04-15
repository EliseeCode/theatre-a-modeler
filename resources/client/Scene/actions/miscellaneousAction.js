

export function initialLoadCSRF() {
    const token = $('.csrfToken').data('csrf-token');
    return {
        type: "LOAD_CSFR",
        payload: { token }
    }
}

export function initialLoadUserData() {
    const userId = $('.user_id').data('user-id');
    return {
        type: "LOAD_USER_ID",
        payload: { userId }
    }
}
