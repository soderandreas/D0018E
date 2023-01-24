import AddCart from './AddCart'

const Items = (item) => {
  return (
    <>
      <div className="item">
        <h2> {item.product} </h2>
        <h3> {item.price} kr </h3>
        <AddCart />
      </div>
    </>
  )
}

export default Items