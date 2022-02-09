enum Role{
    //Can create comment
    STUDENT=1,
    //Can create comment and delete any comment from it's group's play
    TEACHER=2,
    //Can create, delete comment they created
    //Can create post, edit and delete post they created
    EDITOR=3,
    //can do everythingthing
    ADMIN=4
}
export default Role