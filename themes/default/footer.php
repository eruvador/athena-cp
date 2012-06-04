<?php if (!defined('ATHENA_ROOT')) exit; ?>
							</td>
							<td bgcolor="#f5f5f5"></td>
						</tr>

						<tr>
							<td><img src="<?php echo $this->themePath('img/content_bl.gif') ?>" style="display: block" /></td>
							<td bgcolor="#f5f5f5"></td>
							<td><img src="<?php echo $this->themePath('img/content_br.gif') ?>" style="display: block" /></td>
						</tr>
					</table>
				</td>
				<!-- Spacing between content and horizontal end-of-page -->
				<td style="padding: 10px"></td>
			</tr>
			<?php if (Athena::config('ShowCopyright')): ?>
			<tr>
				<td colspan="3"></td>
				<td id="copyright">
					<p>
						<strong>Powered by Athena Control Panel (<?php echo htmlspecialchars(Athena::VERSION) ?><?php echo Athena::SVNVERSION ? '.'.Athena::SVNVERSION : '' ?>)</strong>
						&mdash; Copyright &copy; 2012 Xantara.
					</p>
				</td>
				<td></td>
			</tr>
			<?php endif ?>
			<?php if (Athena::config('ShowRenderDetails')): ?>
			<tr>
				<td colspan="3"></td>
				<td id="info">
					<p>
						Page generated in <strong><?php echo round(microtime(true) - __START__, 5) ?></strong> second(s).
						Number of queries executed: <strong><?php echo (int)Athena::$numberOfQueries ?></strong>.
						<?php if (Athena::config('GzipCompressOutput')): ?>Gzip Compression: <strong>Enabled</strong>.<?php endif ?>
					</p>
				</td>
				<td></td>
			</tr>
			<?php endif ?>
		</table>
	</body>
</html>
