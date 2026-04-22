import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { LearnerService } from './core/learner.service';
import { ThemeService } from './core/theme.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink],
  template: `
    <header class="header">
      <div class="container nav-container">
        <a routerLink="/" class="logo">IT Passport</a>
        
        <div class="nav-actions">
          <div class="id-badge" *ngIf="learnerService.learner() as learner">
            <span class="id-label">Learner ID</span>
            <span class="id-value">{{ learner.id }}</span>
            <button class="btn-icon" (click)="copyId(learner.id)" title="Copy ID">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </button>
            <button class="btn-icon" (click)="switchId()" title="Switch ID">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9"></path><path d="M9 22V12h6v10"></path><path d="M2 10.6L12 2l10 8.6"></path></svg>
            </button>
          </div>

          <button class="btn-icon theme-toggle" (click)="themeService.toggleTheme()">
            <svg *ngIf="!themeService.isDarkMode()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
            <svg *ngIf="themeService.isDarkMode()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
          </button>
        </div>
      </div>
    </header>

    <main class="container">
      <router-outlet></router-outlet>
    </main>

    <footer class="footer">
      <div class="container">
        <p>&copy; 2026 IT Passport Exam Prep. No-Login ID System.</p>
      </div>
    </footer>
  `,
  styles: [`
    .header {
      background-color: var(--surface-bg);
      border-bottom: 1px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 0.75rem;
      padding-bottom: 0.75rem;
    }
    .logo {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--accent-primary);
      text-decoration: none;
    }
    .nav-actions {
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    .id-badge {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      background-color: var(--bg-color);
      padding: 0.25rem 0.5rem 0.25rem 0.75rem;
      border-radius: 999px;
      border: 1px solid var(--border-color);
    }
    .id-label {
      font-size: 0.7rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
    }
    .id-value {
      font-family: monospace;
      font-weight: 700;
      color: var(--text-main);
    }
    main {
      min-height: calc(100vh - 140px);
      padding-top: 2rem;
      padding-bottom: 4rem;
    }
    .footer {
      border-top: 1px solid var(--border-color);
      padding: 2rem 0;
      text-align: center;
      color: var(--text-muted);
      font-size: 0.875rem;
    }
  `]
})
export class App {
  constructor(
    public readonly themeService: ThemeService,
    public readonly learnerService: LearnerService
  ) {}

  copyId(id: number): void {
    navigator.clipboard.writeText(String(id));
    alert('Learner ID copied to clipboard!');
  }

  switchId(): void {
    const newId = prompt('Enter Learner ID to switch to:');
    if (newId) {
      const parsed = Number(newId);
      if (Number.isInteger(parsed) && parsed > 0) {
        this.learnerService.switchLearner(parsed).subscribe({
          error: () => alert('Invalid Learner ID. Please check and try again.')
        });
      } else {
        alert('Please enter a valid numeric ID.');
      }
    }
  }
}
