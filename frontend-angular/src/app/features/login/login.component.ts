import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from '../../core/api.service';

@Component({ selector: 'app-login', imports: [FormsModule], templateUrl: './login.component.html', styleUrl: './login.component.scss' })
export class LoginComponent {
  email = ''; password = ''; loading = false; error = '';
  constructor(private readonly api: ApiService, private readonly router: Router) {}
  ingresar(): void {
    if (!this.email || !this.password || this.loading) return;
    this.loading = true; this.error = '';
    this.api.login(this.email, this.password).subscribe({
      next: ({ token }) => { sessionStorage.setItem('csl_api_token', token); this.router.navigateByUrl('/calificaciones'); },
      error: (response) => { this.error = response.status === 422 || response.status === 401 ? 'Revisa tu correo y contraseña.' : 'No fue posible conectar con el sistema. Intenta de nuevo.'; this.loading = false; },
    });
  }
}
