import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { CreditRequestFormComponent } from './credit-request-form/credit-request-form.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, CreditRequestFormComponent],
  standalone: true,
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {
  title = 'financiacel-frontend';
}
