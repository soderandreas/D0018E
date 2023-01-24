const Header = ({title}) => {
  return (
    <div className="header_container">
        <h1>{title}</h1>
        <button>Create Account</button>
    </div>
  )
}

export default Header