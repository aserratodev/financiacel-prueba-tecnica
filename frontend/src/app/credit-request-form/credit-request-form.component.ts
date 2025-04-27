import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { CommonModule, CurrencyPipe } from '@angular/common';

interface Client {
  id: number;
  name: string;
}

interface Phone {
  id: number;
  model: string;
  price: number;
}

@Component({
  selector: 'app-credit-request-form',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: './credit-request-form.component.html',
  styleUrls: ['./credit-request-form.component.scss'],
  providers: [CurrencyPipe]
})
export class CreditRequestFormComponent implements OnInit {
  clients: Client[] = [];
  phones: Phone[] = [];
  clientId: number | null = null;
  phoneId: number | null = null;
  term: number | null = null;
  monthlyPayment: number | null = null;
  submissionResult: string = '';
  errorMessage: string = '';
  apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient, private currencyPipe: CurrencyPipe, private cdr: ChangeDetectorRef) { }

  ngOnInit(): void {
    this.loadClients();
    this.loadPhones();
  }

  loadClients(): void {
    this.http.get<Client[]>(`${this.apiUrl}/clients`).subscribe({
      next: (data) => {
        this.clients = data;
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar los clientes.';
        console.error('Error loading clients:', error);
      },
    });
  }

  loadPhones(): void {
    this.http.get<Phone[]>(`${this.apiUrl}/phones`).subscribe({
      next: (data) => {
        this.phones = data;
        console.log('this.phones:', this.phones);
      },
      error: (error) => {
        this.errorMessage = 'Error al cargar los modelos de celular.';
        console.error('Error loading phones:', error);
      },
    });
  }

  simulatePayment(): void {
    console.log('Método simulatePayment() llamado');
    if (this.clientId && this.phoneId && this.term) {
      const phoneIdNumber = Number(this.phoneId);
      const selectedPhone = this.phones.find(phone => Number(phone.id) === phoneIdNumber); // Convertir ambos lados a número
      console.log('selectedPhone:', selectedPhone);
      if (selectedPhone) {
        const interestRate = 0.015; // 1.5% mensual
        const principal = selectedPhone.price;
        const n = this.term;
        const monthlyInterest = (principal * interestRate * Math.pow(1 + interestRate, n)) / (Math.pow(1 + interestRate, n) - 1);
        this.monthlyPayment = parseFloat(monthlyInterest.toFixed(2));
        console.log('Cuota mensual simulada:', this.monthlyPayment);
        this.cdr.detectChanges(); // Forzar detección de cambios (temporal)
      } else {
        this.monthlyPayment = null;
      }
    } else {
      this.monthlyPayment = null;
    }
  }

  onSubmit(): void {
    if (this.clientId && this.phoneId && this.term && this.monthlyPayment !== null) {
      const payload = {
        client_id: this.clientId,
        phone_id: this.phoneId,
        term: this.term,
        monthly_payment: this.monthlyPayment
      };

      this.http.post(`${this.apiUrl}/credits`, payload)
        .subscribe({
          next: (response: any) => {
            this.submissionResult = 'Solicitud de crédito creada exitosamente con ID: ' + response.data.id;
            this.errorMessage = '';
          },
          error: (error) => {
            this.errorMessage = error.error.error || error.error.message || 'Error al crear la solicitud de crédito.';
            this.submissionResult = '';
            console.error('Error creating credit:', error);
          },
        });
    } else {
      this.errorMessage = 'Por favor, selecciona cliente, modelo de celular y plazo y simula la cuota.';
      this.submissionResult = '';
    }
  }
}