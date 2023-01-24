import {useState} from 'react'
import Items from './Items'

const Feed = () => {

  const[phones] = useState([
    {
      id: 1,
      product_name: "phone 1",
      price:        "3999"
    },
    {
      id: 2,
      product_name: "phone 2",
      price:        "8999"
    },
    {
      id: 3,
      product_name: "phone 3",
      price:        "5999"
    }
  ])

  return (
    <div className="feed_container">
      {phones.map((phone) => (
        <Items key = {phone.id} product = {phone.product_name} price = {phone.price} />
      ))}
    </div>
  )
}

export default Feed