document.addEventListener('DOMContentLoaded', () => {
  // Function to calculate the total price for a row
  function calculateRowTotal(row) {
    const price = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('$', ''));
    const quantity = parseInt(row.querySelector('.quantity').value);
    const total = price * quantity;
    const totalPriceElement = row.querySelector('.total-price');
    if (totalPriceElement) {
      totalPriceElement.textContent = `$${total.toFixed(2)}`;
    } else {
      console.error('Total price element not found in row:', row);
    }
    return total;
  }

  // Function to update the grand total
  function updateGrandTotal() {
    const rows = document.querySelectorAll('tbody tr');
    let grandTotal = 0;
    rows.forEach(row => {
      grandTotal += calculateRowTotal(row);
    });
    const grandTotalElement = document.getElementById('grand-total');
    if (grandTotalElement) {
      grandTotalElement.textContent = `$${grandTotal.toFixed(2)}`;
    } else {
      console.error('Grand total element not found');
    }
  }

  // Event listener for quantity input changes
  document.querySelectorAll('.quantity').forEach(input => {
    input.addEventListener('change', () => {
      if (parseInt(input.value) < 1) {
        input.value = 1; // Ensure quantity is at least 1
      }
      updateGrandTotal();
    });
  });

  // Calculate grand total on page load
  updateGrandTotal();

  // Handle payment method change
  document.getElementById('paymentMethod').addEventListener('change', (e) => {
    const qrCodeSection = document.getElementById('qrCodeSection');
    if (e.target.value === 'paypal') {
      qrCodeSection.style.display = 'block'; // Show QR code for E-Sewa
    } else {
      qrCodeSection.style.display = 'none'; // Hide QR code for other methods
    }
  });

  // Handle checkout confirmation
  document.getElementById('confirmCheckout').addEventListener('click', () => {
    const address = document.getElementById('address').value;
    const paymentMethod = document.getElementById('paymentMethod').value;
    const notes = document.getElementById('notes').value;

    // Get cart items
    const cartItems = [];
    document.querySelectorAll('tbody tr').forEach(row => {
      const productName = row.querySelector('td:nth-child(1)').textContent;
      const price = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('$', ''));
      const quantity = parseInt(row.querySelector('.quantity').value);
      cartItems.push({ productName, price, quantity });
    });

    console.log('Sending data to server:', { address, paymentMethod, notes, cartItems }); // Debugging

    // Send data to the server
    fetch('process/checkout.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        address,
        paymentMethod,
        notes,
        cartItems,
      }),
    })
      .then(response => {
        console.log('Response received:', response); // Debugging
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Data received:', data); // Debugging
        if (data.success) {
          // Show the success modal
          const successModal = new bootstrap.Modal(document.getElementById('successModal'));
          successModal.show();

          console.log('Success modal shown'); // Debugging

          // Redirect to the cart page after the modal is closed
          document.getElementById('successModal').addEventListener('hidden.bs.modal', () => {
            console.log('Success modal closed, redirecting to cart.php'); // Debugging
            window.location.href = 'cart.php';
          });
        } else {
          console.error('Failed to place order:', data.message); // Debugging
          alert('Failed to place order. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error); // Debugging
        alert('Successfully placed order');
        window.location.href = 'cart.php';
      });
  });
});